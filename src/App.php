<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core {
    
    use Carbon\Carbon;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    
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
         * App constructor.
         */
        public function __construct()
        {
            $environment = config('app.environment', 'development');
            
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
                    'displayErrorDetails' => ($environment === 'production' ? false : true),
                    'addContentLengthHeader' => true,
                    'routerCacheFile' => false,
                ],
            ]);
            
            /**
             * Session
             *
             * Inicia a sessão caso ela esteja habilitada
             */
            
            if (!session_id() && config('app.session')) {
                $current = session_get_cookie_params();
                
                session_set_cookie_params($current['lifetime'], $current['path'], $current['domain'], $current['secure'], true);
                session_name(md5(md5('VCWEB'.$_SERVER['SERVER_NAME'].'/'.$_SERVER['PHP_SELF'])));
                session_cache_limiter('nocache');
                
                session_start();
            }
            
            /**
             * PHP Basic Config
             *
             * Configurações básicas do sistema
             */
            
            mb_internal_encoding('UTF-8');
            date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));
            ini_set('default_charset', 'UTF-8');
            setlocale(LC_ALL, config('app.locale'), config('app.locale').'.utf-8');
            
            /**
             * Carbon
             *
             * Configura a linguagem
             */
            
            Carbon::setLocale(config('app.locale'));
            
            /**
             * ErrorProvider Handler
             *
             * Função customizada para output dos erros
             */
            
            set_error_handler(function ($code, $message, $file, $line) {
                throw new \ErrorException($message, $code, 1, $file, $line);
            });
            
            /**
             * APP Key
             *
             * Gera a chave única caso ela não exista
             */
            
            if (substr(config('app.encryption.key'), 0, 7) !== "base64:") {
                $this->generateKey();
            }
        }
    
        /**
         * Troca o APP_KEY do arquivos .env
         */
        private function generateKey()
        {
            $escaped = preg_quote('='.config('app.encryption.key'), '/');
        
            file_put_contents(
                APP_FOLDER.'/.env',
                preg_replace(
                    "/^APP_KEY".$escaped."/m",
                    'APP_KEY=base64:'.base64_encode(
                        random_bytes(config('app.encryption.cipher') === 'AES-128-CBC' ? 16 : 32)
                    ),
                    file_get_contents(APP_FOLDER.'/.env')
                )
            );
        
            location(BASE_URL, 302);
        }
        
        /**
         * Recupera a instancia da classe
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
         * Adiciona método para criação de rotas simplificado
         *
         * @param string $methods
         * @param string $pattern
         * @param string $controller
         * @param string $name
         * @param string $middleware
         *
         * @return \Slim\Interfaces\RouteInterface
         */
        public function route($methods, $pattern, $controller, $name = null, $middleware = null)
        {
            /**
             * Transforma os métodos passado em array
             */
            
            $methods = explode(',', strtoupper($methods));
            $name = strtolower($name);
            
            /**
             * Mapeamento da rota
             */
            
            $route = $this->map($methods, $pattern, function (Request $request, Response $response, $params) use ($controller) {
                // Verifica se existe métodos customizados
                if (strpos($controller, '@') !== false) {
                    list($controller, $method) = explode('@', $controller);
                } else {
                    $method = null;
                }
                
                /**
                 * Inicia o controller
                 */
                
                $method = mb_strtolower($request->getMethod(), 'UTF-8').ucfirst($method);
                $controller = 'App\\Controllers\\'.str_replace('/', '\\', $controller);
                $controller = new $controller($request, $response, $this);
                
                // Verifica se o método no controller existe
                if (!method_exists($controller, $method)) {
                    throw new \Exception(sprintf(__("O Método '%s::%s' não existe."), get_class($controller), $method));
                }
                
                return call_user_func_array([$controller, $method], $params);
            });
            
            /**
             * Nome da rota
             */
            
            if (!empty($name)) {
                $route->setName($name);
            }
            
            /**
             * Middleware
             */
            
            if (!empty($middleware)) {
                $middlewares = $this->loadRegisters()['middleware']['web'];
                
                if (array_key_exists($middleware, $middlewares)) {
                    $route->add($middlewares[$middleware]);
                }
            }
            
            return $route;
        }
        
        /**
         * Register Middleware for application
         *
         * @return mixed
         */
        public function registerMiddleware()
        {
            $registers = $this->loadRegisters();
            
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
         * Providers, Middlewares, Functions
         *
         * @return array
         */
        private function loadRegisters()
        {
            $registers = [];
            
            if (file_exists(APP_FOLDER.'/bootstrap/registers.php')) {
                $registers = include APP_FOLDER.'/bootstrap/registers.php';
                
                return $registers;
            }
            
            return $registers;
        }
        
        /**
         * Register functions for application
         *
         * @return mixed
         */
        public function registerFunctions()
        {
            $registers = $this->loadRegisters();
            
            if (!empty($registers['functions'])) {
                foreach ((array) $registers['functions'] as $function) {
                    include "{$function}";
                }
            }
        }
        
        /**
         * Register container for application
         *
         * @return mixed
         */
        public function registerProviders()
        {
            $registers = $this->loadRegisters();
            $classes = [];
            
            foreach ($registers['providers'] as $key => $items) {
                foreach ($items as $class) {
                    if (class_exists($class)) {
                        $class = new $class($this->getContainer());
                        $class->register();
                        
                        array_push($classes, $class);
                    }
                }
            }
            
            foreach ($classes as $class) {
                $class->boot();
            }
            
            return $this;
        }
        
        /**
         * Register router for application
         *
         * @return mixed
         */
        public function registerRouter()
        {
            // Router for web
            if (file_exists(APP_FOLDER.'/routes/web.php')) {
                $this->group('', function ($app) {
                    include APP_FOLDER.'/routes/web.php';
                });
            }
            
            // Router for api
            if (file_exists(APP_FOLDER.'/routes/api.php')) {
                $this->group('/api', function ($app) {
                    include APP_FOLDER.'/routes/api.php';
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
            if ($this->resolve($name)) {
                return $this->resolve($name);
            }
        }
        
        /**
         * Resolve a chamada dos containes
         *
         * @param string $name
         * @param array  $params
         *
         * @return mixed|null
         */
        public function resolve($name, $params = [])
        {
            if ($this->getContainer()->has($name)) {
                if (is_callable($this->getContainer()->get($name))) {
                    return call_user_func_array($this->getContainer()->get($name), $params);
                } else {
                    return $this->getContainer()->get($name);
                }
            }
            
            return null;
        }
    }
}
