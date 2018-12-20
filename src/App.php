<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core {
    
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
                    'displayErrorDetails' => (config('app.environment', 'development') === 'development'),
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
         * Inicia as configurações padrão da aplicação
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
            date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));
            setlocale(LC_ALL, config('app.locale'), config('app.locale').'utf-8');
            
            /**
             * Errors
             *
             * Controle de erro do sistema
             */
            
            if (config('app.environment', 'development') === 'development') {
                error_reporting(E_ALL);
            } else {
                error_reporting(E_ALL ^ E_NOTICE);
            }
            
            set_error_handler(function ($code, $message, $file, $line) {
                throw new \ErrorException($message, $code, 1, $file, $line);
            });
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
         * Cria rota personalizadas
         *
         * @param string|array $methods
         * @param string $pattern
         * @param string|\Closure $callable
         * @param string $name
         * @param string|\Closure $middleware
         *
         * @return \Slim\Interfaces\RouteInterface
         */
        public function route($methods, $pattern, $callable, $name = null, $middleware = null)
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
                    
                    if (!method_exists($classObject, $method)) {
                        throw new \BadMethodCallException(sprintf("Method %s::%s not found.", get_class($classObject), $method), E_ERROR);
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
            
            if (!empty($middleware)) {
                if ($middleware instanceof \Closure) {
                    $route->add($middleware);
                } else {
                    $registers = $this->loadRegistersFile();
                    
                    if (!empty($registers['middleware']['web'])) {
                        $middlewares = $registers['middleware']['web'];
                        
                        if (array_key_exists($middleware, $middlewares)) {
                            $route->add($middlewares[$middleware]);
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
            
            if (!empty($registers['middleware']['app'])) {
                foreach ($registers['middleware']['app'] as $key => $middleware) {
                    if (class_exists($middleware)) {
                        $this->add($middleware);
                    }
                }
            }
            
            return $this;
        }
        
        /**
         * Registra as funções
         */
        public function registerFunctions()
        {
            $registers = $this->loadRegistersFile();
            
            if (!empty($registers['functions'])) {
                foreach ($registers['functions'] as $function) {
                    include "{$function}";
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
            $registers = $this->loadRegistersFile();
            $boots = [];
            
            if (!empty($registers['providers'])) {
                foreach ($registers['providers'] as $key => $providers) {
                    foreach ($providers as $provider) {
                        if (class_exists($provider)) {
                            $provider = new $provider($this->getContainer());
                            
                            if (method_exists($provider, 'register')) {
                                $provider->register();
                            }
                            
                            array_push($boots, $provider);
                        }
                    }
                }
                
                foreach ($boots as $provider) {
                    if (method_exists($provider, 'boot')) {
                        $provider->boot();
                    }
                }
            }
            
            return $this;
        }
        
        /**
         * Registra as rotas
         */
        public function registerRouter()
        {
            // Router for web
            if (file_exists(APP_FOLDER.'/routes/web.php')) {
                $this->group('', function ($app) {
                    include_once APP_FOLDER.'/routes/web.php';
                });
            }
            
            // Router for api
            if (file_exists(APP_FOLDER.'/routes/api.php')) {
                $this->group('/api', function ($app) {
                    include_once APP_FOLDER.'/routes/api.php';
                });
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
            }
            
            return $container->get($name);
        }
    }
}
