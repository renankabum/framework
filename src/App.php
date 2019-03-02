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
        private static $instance;
        
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
            if ($callable instanceof \Closure) {
                $route = $this->map($methods, $pattern, $callable);
            } else {
                $route = $this->map($methods, $pattern, function (Request $request, Response $response, array $params) use ($callable) {
                    // Separa o namespace e método
                    list($namespace, $originalMethod) = (explode('@', $callable) + [1 => null]);
                    $method = mb_strtolower($request->getMethod()).ucfirst($originalMethod);
                    
                    // Inicia controller
                    $namespace = "App\\Controllers\\".str_ireplace('/', '\\', $namespace);
                    $controller = new $namespace($request, $response, $this);
                    
                    // Verifica se existe o método
                    if (!Helper::checkMethods($controller, [$method, '__call', '__callStatic'])) {
                        // Verifica se o método original existe
                        $method = ($originalMethod ?: 'index');
                        
                        if (!method_exists($controller, $method)) {
                            throw new \BadMethodCallException(
                                sprintf("Call to undefined method %s::%s()", get_class($controller), $method), E_ERROR
                            );
                        }
                    }
                    
                    return call_user_func_array([$controller, $method], $params);
                });
            }
            
            // Adiciona o nome na rota
            if (!empty($name)) {
                $name = mb_strtolower($name);
                $route->setName($name);
            }
            
            // Adiciona middlewares na rota
            if (!empty($middlewares)) {
                $middlewaresManual = config('app.middlewares.manual', []);
                
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
         * Inicia as configurações padrões da aplicação
         */
        public function initConfigs()
        {
            /**
             * PHP Basic Config
             *
             * Configurações básicas da aplicação
             */
            
            ini_set('default_charset', 'UTF-8');
            mb_internal_encoding('UTF-8');
            date_default_timezone_set(env('APP_TIMEZONE', 'America/Sao_Paulo'));
            setlocale(LC_ALL, env('APP_LOCALE'), env('APP_LOCALE').'utf-8');
            
            /**
             * Errors
             *
             * Controle de erro do sistema
             */
            
            ini_set('display_errors', 'On');
            ini_set('display_startup_errors', 'On');
            
            if (env('APP_ENV', 'development') === 'development') {
                error_reporting(E_ALL ^ E_DEPRECATED);
            } else {
                error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
            }
            
            if (env('APP_SET_ERROR_HANDLER', true) == 'true') {
                set_error_handler(function ($code, $message, $file, $line) {
                    if (!(error_reporting() & $code)) {
                        return;
                    }
                    
                    throw new \ErrorException($message, $code, 1, $file, $line);
                });
            }
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
        
        /**
         * Inicia as rotas padrão da aplicação
         */
        public function initRoutes()
        {
            $includeOnce = function ($file, $app) {
                include_once "{$file}";
            };
            
            foreach (glob_recursive(APP_FOLDER."/routes/**") as $file) {
                if (is_file($file) && !is_dir($file)) {
                    $includeOnce($file, $this);
                }
            }
        }
        
        /**
         * Inicia os serviços da aplicação
         *
         * @param array $providers
         */
        public function initProviders($providers = [])
        {
            // Providers padrões ou passados por parametro
            $providers = $providers ?: config('app.providers', []);
            
            // Percorre os providers
            foreach ($providers as $provider) {
                if (class_exists($provider)) {
                    $provider = new $provider($this->getContainer());
                    
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
        public function initMiddlewares($middlewares = [])
        {
            // Middlewares padrões ou passadas por parametro
            $middlewares = $middlewares ?: config('app.middlewares.automatic', []);
            
            // Percorre as middlewares
            foreach ($middlewares as $name => $middleware) {
                if (class_exists($middleware)) {
                    $this->add($middleware);
                }
            }
        }
    }
}
