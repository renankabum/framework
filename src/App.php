<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
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
    final class App extends \Slim\App
    {
        /**
         * @var \Core\App
         */
        private static $instance = null;
        
        /**
         * App constructor.
         *
         * @internal param array $container
         */
        public function __construct()
        {
            /**
             * Start session
             */
            if (!session_id() && config('app.session')) {
                $current = session_get_cookie_params();
                
                session_set_cookie_params($current['lifetime'], $current['path'], $current['domain'], $current['secure'], true);
                session_name(md5(md5('VCWEB_APP'.$_SERVER['SERVER_NAME'].'/'.$_SERVER['PHP_SELF'])));
                session_cache_limiter('nocache');
                
                session_start();
            }
            
            /**
             * Error app
             */
            set_error_handler(function ($code, $message, $file, $line) {
                if (!($code & error_reporting())) {
                    return;
                }
                
                throw new \ErrorException($message, $code, 0, $file, $line);
            });
            
            /**
             * ConfigurationMiddleware timezone app
             */
            date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));
            
            /**
             * Locale language
             */
            setlocale(LC_ALL, config('app.locale'), config('app.locale').'.utf-8');
            Carbon::setLocale(config('app.locale'));
            
            /**
             * ConfigurationMiddleware default charset app
             */
            ini_set('default_charset', 'UTF-8');
            
            /**
             * Char set mb internal
             */
            mb_internal_encoding('UTF-8');
            
            /**
             * Switch environment app
             */
            switch (config('app.environment', 'production')) {
                case 'development';
                    ini_set('error_reporting', -1);
                    ini_set('display_errors', 1);
                    /*error_reporting(E_ALL);*/
                    break;
                
                case 'production':
                    ini_set('error_reporting', 0);
                    ini_set('display_errors', 0);
                    /*error_reporting(E_ERROR);*/
                    break;
                
                default:
                    header('HTTP/1.1 503 Service Unavailable', true, 503);
                    echo 'The application environment is not set correctly.';
                    die(1);
                    break;
            }
            
            /**
             * Slim setup
             */
            $settings = [
                'settings' => [
                    'httpVersion' => '1.1',
                    'responseChunkSize' => 4096,
                    'outputBuffering' => 'append',
                    'determineRouteBeforeAppMiddleware' => true,
                    'displayErrorDetails' => (config('app.environment') === 'production' ? false : true),
                    'addContentLengthHeader' => false,
                    'routerCacheFile' => false,
                ],
            ];
            
            /**
             * Merge all settings
             */
            //$settings = array_merge($settings, config());
            
            /**
             * Slim parent construct
             */
            parent::__construct($settings);
            
            /**
             * Generate encryption key
             */
            if (substr(config('app.encryption.key'), 0, 7) !== "base64:") {
                $this->generateKey();
            }
            
            /**
             * Regenerate encryption key in days
             */
            /*if (config('app.encryption.regenerate.days') === true) {
                $time = Carbon::now()
                    ->getTimestamp();

                $newTime = Carbon::now()
                    ->addDays(config('app.encryption.regenerate.days'))
                    ->getTimestamp();

                if ($time > $newTime) {
                    $this->generateKey();
                }
            }*/
        }
        
        /**
         * Customized route mapping for a cleaner workflow.
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
            // Transform methods in array
            $methods = explode(',', strtoupper($methods));
            $name = strtolower($name);
            
            // Mapping routers
            $map = $this->map($methods, $pattern, function (Request $request, Response $response, $params) use ($controller) {
                if (strpos($controller, '@') === false) {
                    $method = null;
                } else {
                    list($controller, $method) = explode('@', $controller);
                }
                
                // Método criado conforme o request
                $method = mb_strtolower($request->getMethod(), 'UTF-8').ucfirst($method);
                
                $controller = 'App\\Controllers\\'.str_replace('/', '\\', $controller);
                $controller = new $controller($request, $response, $params, $this);
                
                if (!method_exists($controller, $method)) {
                    throw new \Exception('O Método <b>'.get_class($controller).'::'.$method.'</b> não existe.');
                }
                
                return call_user_func_array([$controller, $method], $params);
            });
            
            /**
             * Support a name in route
             */
            if (!is_null($name)) {
                $map->setName($name);
            }
            
            /**
             * Support middleware in route
             */
            if (!is_null($middleware)) {
                $middlewares = $this->getRegisters()['middleware']['web'];
                
                if (array_key_exists($middleware, $middlewares)) {
                    $map->add($middlewares[$middleware]);
                }
            }
            
            return $map;
        }
        
        /**
         * Resolve callable for container
         *
         * @param string $id
         * @param array  $param_arr
         *
         * @return mixed|null
         */
        public function resolve($id, $param_arr = [])
        {
            if ($this->getContainer()
                ->has($id)) {
                if (is_callable($this->getContainer()
                    ->get($id))) {
                    return call_user_func_array($this->getContainer()
                        ->get($id), $param_arr);
                } else {
                    return $this->getContainer()
                        ->get($id);
                }
            }
            
            return null;
        }
        
        /**
         * Get instance class
         *
         * @return \Core\App
         */
        public static function getInstance()
        {
            if (self::$instance === null) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }
        
        /**
         * Register Middleware for application
         *
         * @return mixed
         */
        public function registerMiddleware()
        {
            $registers = $this->getRegisters();
            
            if (!empty($registers['middleware']['app'])) {
                foreach ((array)$registers['middleware']['app'] as $key => $class) {
                    if (class_exists($class)) {
                        $this->add(new $class($this->getContainer()));
                    }
                }
            }
        }
        
        /**
         * Register functions for application
         *
         * @return mixed
         */
        public function registerFunctions()
        {
            $registers = $this->getRegisters();
            
            if (!empty($registers['functions'])) {
                foreach ((array)$registers['functions'] as $function) {
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
            $registers = $this->getRegisters();
            
            $providers = [];
            foreach ((array)$registers['providers'] as $key => $items) {
                if (is_array($items)) {
                    foreach ($items as $class) {
                        if (class_exists($class)) {
                            /** @var \Core\Contracts\Provider $provider */
                            $provider = new $class($this->getContainer());
                            $provider->register();
                            
                            array_push($providers, $provider);
                        }
                    }
                }
            }
            
            foreach ($providers as $provider) {
                $provider->boot();
            }
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
        
        // PRIVATE
        
        /**
         * Get middleware & container
         *
         * @return array
         */
        private function getRegisters()
        {
            $array = [];
            
            if (file_exists(APP_FOLDER.'/bootstrap/registers.php')) {
                $array = include APP_FOLDER.'/bootstrap/registers.php';
                
                return $array;
            }
            
            return $array;
        }
        
        /**
         * Troca o APP_KEY do arquivos .env
         */
        private function generateKey()
        {
            file_put_contents(APP_FOLDER.'/.env', preg_replace(
                $this->keyReplacementPattern(), 'APP_KEY=base64:'.base64_encode(
                    random_bytes(config('app.encryption.cipher') === 'AES-128-CBC' ? 16 : 32)
                ), file_get_contents(APP_FOLDER.'/.env')
            ));
            
            location(BASE_URL, 302);
        }
        
        /**
         * @return string
         */
        private function keyReplacementPattern()
        {
            $escaped = preg_quote('='.config('app.encryption.key'), '/');
            
            return "/^APP_KEY".$escaped."/m";
        }
        
        /**
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            if (is_object($this->resolve($name))) {
                return $this->resolve($name);
            }
        }
    }
}
