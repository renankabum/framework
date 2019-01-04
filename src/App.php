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
                        throw new \BadMethodCallException(sprintf("Method %s::%s() not found.", get_class($classObject), $method), E_ERROR);
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
                $middlewaresManual = config('app.middlewares.manual', []);
                
                if (!is_array($middlewares)) {
                    $middlewares = [$middlewares];
                }
                
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
            
            set_error_handler(function ($code, $message, $file, $line) {
                throw new \ErrorException($message, $code, 1, $file, $line);
            });
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
        
        /**
         * Verifica os métodos antigo da classe e converte para o novo
         *
         * @param string $method
         * @param mixed $parameters
         *
         * @return array|bool|string
         */
        
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
         */
        public function initProviders()
        {
            $providers = [];
            
            foreach (config('app.providers', []) as $provider) {
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
         * Inicia as middleware da aplicação
         */
        public function initMiddlewares()
        {
            foreach (config('app.middlewares.automatic', []) as $name => $middleware) {
                if (class_exists($middleware)) {
                    $this->add($middleware);
                }
            }
        }
        
        /**
         * @param string $method
         * @param mixed $parameters
         *
         * @return array|bool|string
         */
        public function __call($method, $parameters)
        {
            $parameter = null;
            
            /*if (!empty($parameters[0])) {
                $parameter = $parameters[0];
            }*/
            
            switch ($method) {
                case 'registerRouter':
                    return $this->initRoutes();
                    break;
                
                case 'registerProviders':
                    return $this->initProviders();
                    break;
                
                case 'registerMiddleware':
                    return $this->initMiddlewares();
                    break;
                
                case 'registerFunctions':
                case 'loadRegistersFile':
                    return '';
                    break;
            }
            
            if (!method_exists(get_class(), $method)) {
                throw new \BadMethodCallException(sprintf("Call to undefined method %s::%s()", get_class($this), $method), E_ERROR);
            }
        }
    }
}
