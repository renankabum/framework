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

namespace Core;

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
    /** @var null */
    private static $instance = null;
    
    /**
     * App constructor.
     *
     * @internal param array $container
     */
    public function __construct()
    {
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
        $settings = array_merge($settings, config());
        
        /**
         * Slim parent construct
         */
        parent::__construct($settings);
        
        /**
         * Generate encryption key
         */
        if (empty(config('app.encryption.key'))) {
            $this->generateKey();
        }
        
        /**
         * Regenerate encryption key in days
         */
        if (config('app.encryption.regenerate.days') !== false) {
            $time = Carbon::now()->getTimestamp();
            $newTime = Carbon::now()->addDays(config('app.encryption.regenerate.days'))->getTimestamp();
            
            if ($time > $newTime) {
                $this->generateKey();
            }
        }
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
     * Get version application
     */
    public function version()
    {
        return config('app.version');
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
        if ($this->getContainer()->has($id)) {
            if (is_callable($this->getContainer()->get($id))) {
                return call_user_func_array(
                    $this->getContainer()->get($id), $param_arr
                );
            } else {
                return $this->getContainer()->get($id);
            }
        }
        
        return null;
    }
    
    /**
     * Customized route mapping for a cleaner workflow.
     *
     * @param string $methods
     * @param string $pattern
     * @param string $controller
     *
     * @return \Slim\Interfaces\RouteInterface
     */
    public function route($methods, $pattern, $controller)
    {
        // Transform methods in array
        $methods = explode(',', strtoupper($methods));
        
        // Mapping routers
        return $this->map(
            $methods, $pattern, function (Request $request, Response $response, $params) use ($controller) {
    
            if (strpos($controller, '@') === false) {
                $class = $controller;
                $method = null;
            } else {
                list($class, $method) = explode('@', $controller);
            }
    
            //$controller = 'App\\Http\\Controllers\\' . str_replace('/', '\\', $class);
            $controller = 'App\\Controllers\\' . str_replace('/', '\\', $class);
            $object = new $controller($request, $response, $params, $this);
    
            if (!method_exists($object, strtolower($request->getMethod()) . ucfirst($method))) {
                throw new \Exception('Method <b>' . get_class($object) . '::' . strtolower($request->getMethod()) . ucfirst($method) . '</b> does not exists!');
            }
    
            return call_user_func_array([$object, strtolower($request->getMethod()) . ucfirst($method),], $params);
        }
        );
    }
    
    /**
     * Register Middleware for application
     *
     * @return mixed
     */
    public function registerMiddleware()
    {
        $registers = $this->getRegisters();
    
        foreach ((array) $registers['middleware'] as $class) {
            if (class_exists($class)) {
                $this->add(new $class($this->getContainer()));
            }
        }
    }
    
    /**
     * Register container for application
     *
     * @return mixed
     */
    public function registerContainer()
    {
        $registers = $this->getRegisters();
        
        $providers = [];
        foreach ((array) $registers['providers'] as $class) {
            if (class_exists($class)) {
    
                /** @var \Core\Contracts\ServiceProviderAbstract $provider */
                $provider = new $class();
                $provider->register($this->getContainer());
                
                array_push($providers, $provider);
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
        /** @var \Core\App $app */
        $app = $this;
        
        // Router for web
        if (file_exists(APP_FOLDER . '/routes/web.php')) {
            $this->group(
                '', function () use ($app) {
                include APP_FOLDER . '/routes/web.php';
            }
            );
        }
        
        // Router for api
        if (file_exists(APP_FOLDER . '/routes/api.php')) {
            $this->group(
                '/api', function () use ($app) {
                include APP_FOLDER . '/routes/api.php';
            }
            );
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
    
        if (file_exists(APP_FOLDER . '/bootstrap/registers.php')) {
            $array = include APP_FOLDER . '/bootstrap/registers.php';
            
            return $array;
        }
        
        return $array;
    }
    
    /**
     *
     */
    private function generateKey()
    {
        file_put_contents(
            APP_FOLDER . '/.env', preg_replace(
                $this->keyReplacementPattern(), 'APP_KEY=base64:' . base64_encode(
                    random_bytes(
                        config('app.encryption.cipher') === 'AES-128-CBC' ? 16 : 32
                    )
                ), file_get_contents(APP_FOLDER . '/.env')
            )
        );
    }
    
    /**
     * @return string
     */
    private function keyReplacementPattern()
    {
        $escaped = preg_quote('=' . config('app.encryption.key'), '/');
        
        return "/^APP_KEY{$escaped}/m";
    }
}
