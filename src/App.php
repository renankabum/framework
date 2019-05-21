<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 14/04/2019 Vagner Cardoso
 */

namespace Core {
    
    use BadMethodCallException;
    use Closure;
    use Core\Helpers\Helper;
    use Dotenv\Dotenv;
    use Dotenv\Environment\Adapter\EnvConstAdapter;
    use Dotenv\Environment\Adapter\PutenvAdapter;
    use Dotenv\Environment\DotenvFactory;
    use ErrorException;
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
        private static $instance;
        
        /**
         * App constructor
         */
        public function __construct()
        {
            // Dotenv
            $this->initDotenv();
            
            // Configuraçoes do slim
            parent::__construct([
                'settings' => array_merge([
                    'httpVersion' => '1.1',
                    'responseChunkSize' => 4096,
                    'outputBuffering' => 'append',
                    'determineRouteBeforeAppMiddleware' => true,
                    'displayErrorDetails' => (env('APP_ENV', 'development') == 'development'),
                    'addContentLengthHeader' => true,
                    'routerCacheFile' => false,
                ], config('app.slim', [])),
            ]);
            
            // Configuração da aplicação
            $this->initConfigs();
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
         * Inicia as configurações de environment
         */
        public function initDotenv()
        {
            $pathEnv = APP_FOLDER.'/.env';
            
            if (file_exists($pathEnv)) {
                Dotenv::create(APP_FOLDER, '.env', new DotenvFactory([
                    new EnvConstAdapter(),
                    new PutenvAdapter(),
                ]))->overload();
            } else {
                $pathExample = APP_FOLDER.'/.env-example';
                
                if (!file_exists($pathEnv) && (file_exists($pathExample) && !is_dir($pathExample))) {
                    file_put_contents(
                        $pathEnv, file_get_contents($pathExample), FILE_APPEND
                    );
                }
            }
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
            
            $charset = env('APP_CHARSET', 'UTF-8');
            $locale = env('APP_LOCALE', 'pt_BR');
            
            ini_set('default_charset', $charset);
            mb_internal_encoding($charset);
            date_default_timezone_set(env('APP_TIMEZONE', 'America/Sao_Paulo'));
            setlocale(LC_ALL, $locale, "{$locale}.{$charset}");
            
            /**
             * Errors
             *
             * Controle de erro do sistema
             */
            
            ini_set('log_errors', (env('INI_LOG_ERRORS', 'true') == 'true'));
            ini_set('error_log', sprintf(env('INI_ERROR_LOG', APP_FOLDER.'/storage/logs/php-%s.log'), date('dmY')));
            ini_set('display_errors', env('INI_DISPLAY_ERRORS', 'On'));
            ini_set('display_startup_errors', env('INI_DISPLAY_STARTUP_ERRORS', 'On'));
            
            if (env('APP_ENV', 'development') == 'development') {
                error_reporting(E_ALL ^ E_DEPRECATED);
            } else {
                error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
            }
            
            if (env('APP_SET_ERROR_HANDLER', 'true') == 'true') {
                set_error_handler(function ($level, $message, $file = '', $line = 0) {
                    if (error_reporting() & $level) {
                        throw new ErrorException(
                            $message, 0, $level, $file, $line
                        );
                    }
                });
            }
        }
        
        /**
         * Inicia as funções da aplicação
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
         * Inicia os serviços da aplicação
         *
         * @param array $providers
         */
        public function registerProviders($providers = [])
        {
            if (empty($providers)) {
                $registers = $this->loadRegistersFile();
                $registerProviders = (!empty($registers['providers']) ? $registers['providers'] : []);
                
                // Monta os serviços antigos/novos
                foreach ($registerProviders as $provider) {
                    if (is_array($provider)) {
                        foreach ($provider as $item) {
                            $providers[] = $item;
                        }
                    } else {
                        $providers[] = $provider;
                    }
                }
            }
            
            // Percore os serviços
            foreach ($providers as $provider) {
                if (class_exists($provider)) {
                    $provider = new $provider(
                        $this->getContainer()
                    );
                    
                    // Verifica método `register`
                    if (method_exists($provider, 'register')) {
                        $provider->register();
                    }
                    
                    // Verifica método `boot`
                    if (method_exists($provider, 'boot')) {
                        $provider->boot();
                    }
                }
            }
        }
        
        /**
         * Inicia as middleware da aplicação
         *
         * @param array $middlewares
         */
        public function registerMiddleware($middlewares = [])
        {
            if (empty($middlewares)) {
                $registers = $this->loadRegistersFile();
                $middlewares = (!empty($registers['middleware']['app'])
                    ? $registers['middleware']['app']
                    : []);
            }
            
            foreach ($middlewares as $key => $middleware) {
                if (class_exists($middleware)) {
                    $this->add($middleware);
                }
            }
        }
        
        /**
         * Inicia as rotas padrão da aplicação
         */
        public function registerRouter()
        {
            $include = function ($file, $api, $app) {
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
                    $include($file, strpos($file, 'api.php'), $this);
                }
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
            // Variavéis
            $methods = (is_string($methods) ? explode(',', mb_strtoupper($methods)) : $methods);
            $pattern = (string) $pattern;
            
            // Verifica se o callable e uma closure
            if ($callable instanceof Closure) {
                $route = $this->map($methods, $pattern, $callable);
            } else {
                $route = $this->map($methods, $pattern, function (Request $request, Response $response, array $params) use ($callable) {
                    // Separa o namespace e método
                    list($namespace, $originalMethod) = (explode('@', $callable) + [1 => null]);
                    $method = mb_strtolower($request->getMethod()).ucfirst($originalMethod);
                    
                    // Valida se existe o `Controller` na string
                    if (!strripos($namespace, 'Controller')) {
                        $namespace = "{$namespace}Controller";
                    }
                    
                    /**
                     * Percorre os grupos procurando
                     * por NAMESPACES para auto completar
                     *
                     * @var \Slim\Route $route
                     */
                    if ($route = $request->getAttribute('route')) {
                        foreach (array_reverse($route->getGroups()) as $group) {
                            if (property_exists($group, 'namespaces')) {
                                foreach ($group->namespaces as $n) {
                                    $n = (($n[strlen($n) - 1] !== '/') ? "{$n}/" : $n);
                                    $namespace = "{$n}{$namespace}";
                                }
                            }
                        }
                    }
                    
                    // Inicia o controller
                    $namespace = "App\\Controllers\\".str_ireplace('/', '\\', $namespace);
                    $controller = new $namespace($request, $response, $this);
                    
                    // Verifica se existe o método
                    if (!Helper::checkMethods($controller, [$method, '__call', '__callStatic'])) {
                        // Verifica se o método original existe
                        $method = ($originalMethod ?: 'index');
                        
                        if (!method_exists($controller, $method)) {
                            throw new BadMethodCallException(
                                sprintf("Call to undefined method %s::%s()", get_class($controller), $method), E_ERROR
                            );
                        }
                    }
                    
                    return call_user_func_array(
                        [$controller, $method], $params
                    );
                });
            }
            
            // Adiciona o nome na rota
            if (!empty($name)) {
                $name = mb_strtolower($name);
                $route->setName($name);
            }
            
            // Adiciona middlewares na rota
            if (!empty($middlewares)) {
                $registers = $this->loadRegistersFile();
                $middlewaresManual = (!empty($registers['middleware']['web']) ? $registers['middleware']['web'] : []);
                
                if (!is_array($middlewares)) {
                    $middlewares = [$middlewares];
                }
                
                sort($middlewares);
                
                foreach ($middlewares as $middleware) {
                    if ($middleware instanceof Closure) {
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
         * @param array $resources
         */
        public function resources(array $resources)
        {
            if (!empty($resources)) {
                foreach ($resources as $pattern => $item) {
                    // Variávies
                    $name = null;
                    $controller = $item;
                    $middlewares = null;
                    
                    // Caso seja um array, trate.
                    if (is_array($item)) {
                        $name = (!empty($item[1]) ? $item[1] : null);
                        $controller = (!empty($item[0]) ? $item[0] : null);
                        $middlewares = (!empty($item[2]) ? $item[2] : null);
                    }
                    
                    // Cria as rotas
                    $this->resource($pattern, $controller, $name, $middlewares);
                }
            }
        }
        
        /**
         * @param string|array $pattern
         * @param callable|\Closure $callable
         *
         * @return \Slim\Interfaces\RouteGroupInterface
         */
        public function group($pattern, $callable)
        {
            // Variávies
            $namespace = null;
            
            // Verifica o pattern e caso
            // seja um array, trata
            if (!empty($pattern) && is_array($pattern)) {
                if (!empty($pattern['namespace'])) {
                    $namespace = $pattern['namespace'];
                }
                
                $pattern = (!empty($pattern['prefix'])
                    ? $pattern['prefix']
                    : '');
            }
            
            // Executa o group
            $group = parent::group($pattern, $callable);
            
            // namespaces
            if (!empty($namespace)) {
                $group->namespaces[] = $namespace;
            }
            
            return $group;
        }
        
        /**
         * Resolve as chamada dos container
         *
         * @param string $name
         * @param array $params
         *
         * @return mixed
         */
        public function resolve($name, $params = [])
        {
            $container = $this->getContainer();
            
            if ($container->has($name)) {
                if (is_callable($container->get($name))) {
                    return call_user_func_array(
                        $container->get($name), $params
                    );
                }
                
                return $container->get($name);
            }
            
            return false;
        }
        
        /**
         * Providers, Middlewares, Functions
         *
         * @return array
         */
        private function loadRegistersFile()
        {
            // Variávies
            $registers = [];
            $path = APP_FOLDER.'/bootstrap/registers.php';
            
            if (file_exists($path)) {
                $registers = include $path;
            }
            
            return $registers;
        }
    }
}
