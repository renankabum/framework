<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 28/04/2017 Vagner Cardoso
 */

namespace Core {
    
    use Core\Helpers\Helper;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class App
     *
     * @package Core
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class App extends \Slim\App
    {
        /**
         * @var \Core\App
         */
        protected static $instance;
        
        /**
         * App constructor
         */
        public function __construct()
        {
            /**
             * Slim
             *
             * Configurações padrões
             */
            
            parent::__construct([
                'settings' => [
                    'httpVersion' => '1.1',
                    'responseChunkSize' => 4096,
                    'outputBuffering' => 'append',
                    'determineRouteBeforeAppMiddleware' => true,
                    'displayErrorDetails' => (env('APP_ENV', 'development') === 'development'),
                    'addContentLengthHeader' => true,
                    'routerCacheFile' => false,
                ],
            ]);
            
            /**
             * Inicia as configurações
             */
            
            $this->initConfigs();
        }
        
        /**
         * Inicia as configurações padrões da aplicação
         */
        public function initConfigs()
        {
            /**
             * PHP Basic Config
             *
             * Configurações básicas da aplicação
             */
            
            ini_set('display_errors', 'On');
            ini_set('display_startup_errors', 'On');
            ini_set('default_charset', 'UTF-8');
            
            mb_internal_encoding('UTF-8');
            date_default_timezone_set(env('APP_TIMEZONE', 'America/Sao_Paulo'));
            setlocale(LC_ALL, env('APP_LOCALE'), env('APP_LOCALE').'utf-8');
            
            /**
             * Errors
             *
             * Controle de erro do sistema
             */
            
            if (env('APP_ENV') === 'development') {
                error_reporting(E_ALL);
            } else {
                error_reporting(E_ALL ^ E_NOTICE);
            }
            
            if (env('APP_SET_ERROR_HANDLER', true)) {
                set_error_handler(function ($code, $message, $file, $line) {
                    throw new \ErrorException($message, $code, 1, $file, $line);
                });
            }
        }
        
        /**
         * Recupera a instância da classe
         *
         * @return \Core\App
         */
        public static function getInstance()
        {
            if (empty(self::$instance)) {
                self::$instance = new self();
            }
            
            return self::$instance;
        }
        
        /**
         * @param string $pattern
         * @param string $controller
         * @param string|null $name
         * @param string|array|null $middlewares
         */
        public function resource($pattern, $controller, $name = null, $middlewares = null)
        {
            // Verifica o nome da rota
            if (empty($name)) {
                $name = str_replace('/', '.', $pattern);
                
                if ($name[0] === '.') {
                    $name = substr($name, 1);
                }
            }
            
            // Ações
            $actions = [
                ['get', "{$pattern}/create", 'create'],
                ['get', "{$pattern}/{id}/edit", 'edit'],
                ['get,post,put,delete,options,patch', "{$pattern}[/{id}]"],
            ];
            
            // Percore as ações criando as rotas
            foreach ($actions as $action) {
                $callable = (!empty($action[2]) ? "{$controller}@{$action[2]}" : $controller);
                $rname = (!empty($action[2]) ? "{$name}.{$action[2]}" : $name);
                
                $this->route($action[0], $action[1], $callable, $rname, $middlewares);
            }
        }
        
        /**
         * Cria rota personalizadas
         *
         * @param string|array $methods
         * @param string $pattern
         * @param string|\Closure $callable
         * @param string $name
         * @param string|array|\Closure $middlewares
         *
         * @return \Slim\Interfaces\RouteInterface
         */
        public function route($methods, $pattern, $callable, $name = null, $middlewares = null)
        {
            /**
             * Trata argumentos
             */
            
            $methods = (is_string($methods) ? explode(',', mb_strtoupper($methods)) : $methods);
            $pattern = (string) $pattern;
            
            /**
             * Verifica se o callable e uma closure
             */
            
            if ($callable instanceof \Closure) {
                $route = $this->map($methods, $pattern, $callable);
            } else {
                $route = $this->map($methods, $pattern, function (Request $request, Response $response, array $params) use ($callable) {
                    /**
                     * Separa o callable
                     */
                    
                    if (mb_strpos($callable, '@')) {
                        list($class, $method) = explode('@', $callable);
                    } else {
                        $class = $callable;
                        $method = null;
                    }
                    
                    /**
                     * Inicia o controller
                     */
                    
                    $method = mb_strtolower($request->getMethod()).ucfirst($method);
                    $class = "App\\Controllers\\".str_ireplace('/', '\\', $class);
                    $classObject = new $class($request, $response, $this);
                    
                    /**
                     * Verifica método
                     */
                    
                    if (!Helper::checkMethods($classObject, [$method, '__call', '__callStatic'])) {
                        throw new \BadMethodCallException(sprintf("Call to undefined method %s::%s()", get_class($classObject), $method), E_ERROR);
                    }
                    
                    return call_user_func_array([$classObject, $method], $params);
                });
            }
            
            /**
             * Verifica se foi passado o nome
             */
            
            if (!empty($name)) {
                $name = mb_strtolower($name);
                $route->setName($name);
            }
            
            /**
             * Verifica se foi passado a middleware
             */
            
            if (!empty($middlewares)) {
                $registers = $this->loadRegistersFile();
                $middlewaresManual = (!empty($registers['middleware']['web']) ? $registers['middleware']['web'] : []);
                
                if (!is_array($middlewares)) {
                    $middlewares = [$middlewares];
                }
                
                sort($middlewares);
                
                foreach ($middlewares as $middleware) {
                    if ($middleware instanceof \Closure) {
                        $route->add($middleware);
                    } else {
                        if (array_key_exists($middleware, $middlewaresManual)) {
                            $route->add($middlewaresManual[$middleware]);
                        }
                    }
                }
            }
            
            return $route;
        }
        
        /**
         * Providers, Middlewares, Functions
         *
         * @return array
         */
        private function loadRegistersFile()
        {
            $registers = [];
            
            if (file_exists(APP_FOLDER.'/bootstrap/registers.php')) {
                $registers = include APP_FOLDER.'/bootstrap/registers.php';
                
                return $registers;
            }
            
            return $registers;
        }
        
        /**
         * Registra as middlewares
         *
         * @return mixed
         */
        public function registerMiddleware()
        {
            $registers = $this->loadRegistersFile();
            $middlewares = (!empty($registers['middleware']['app']) ? $registers['middleware']['app'] : []);
            
            foreach ($middlewares as $key => $middleware) {
                if (class_exists($middleware)) {
                    $this->add($middleware);
                }
            }
        }
        
        /**
         * Registra as funções
         */
        public function registerFunctions()
        {
            $registers = $this->loadRegistersFile();
            
            if (!empty($registers['functions'])) {
                foreach ($registers['functions'] as $function) {
                    include_once "{$function}";
                }
            }
        }
        
        /**
         * Registra os serviços
         *
         * @return mixed
         */
        public function registerProviders()
        {
            $providers = [];
            $registers = $this->loadRegistersFile();
            $registerProviders = (!empty($registers['providers']) ? $registers['providers'] : []);
            $arrayProviders = [];
            
            // Monta os serviços
            foreach ($registerProviders as $provider) {
                if (is_array($provider)) {
                    foreach ($provider as $item) {
                        $arrayProviders[] = $item;
                    }
                } else {
                    $arrayProviders[] = $provider;
                }
            }
            
            // Percore os serviços
            foreach ($arrayProviders as $provider) {
                if (class_exists($provider)) {
                    $provider = new $provider($this->getContainer());
                    
                    if (method_exists($provider, 'register')) {
                        $provider->register();
                    }
                    
                    array_push($providers, $provider);
                }
            }
            
            foreach ($providers as $provider) {
                if (method_exists($provider, 'boot')) {
                    $provider->boot();
                }
            }
        }
        
        /**
         * Registra as rotas
         */
        public function registerRouter()
        {
            $includeOnce = function ($file, $app, $api) {
                if ($api !== false) {
                    $this->group('/api', function ($app) use ($file) {
                        include_once "{$file}";
                    });
                } else {
                    include_once "{$file}";
                }
            };
            
            foreach (glob_recursive(APP_FOLDER."/routes/**") as $file) {
                if (is_file($file) && !is_dir($file)) {
                    $includeOnce($file, $this, strpos($file, 'api.php'));
                }
            }
        }
        
        /**
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            return $this->resolve($name);
        }
        
        /**
         * Resolve as chamada dos container
         *
         * @param string $name
         * @param array $params
         *
         * @return mixed
         */
        public function resolve($name = null, $params = [])
        {
            $container = $this->getContainer();
            
            if ($container->has($name)) {
                if (is_callable($container->get($name))) {
                    return call_user_func_array($container->get($name), $params);
                }
                
                return $container->get($name);
            }
            
            return false;
        }
    }
}
