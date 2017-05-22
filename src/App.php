<?php

/**
 * NAVEGARTE Networks
 *
 * @package   FrontEnd
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class App
 *
 * @package Navegarte
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
    // Slim setup
    $settings = [
      'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => (config('app.environment') === 'production' ? false : true),
        'addContentLengthHeader' => false,
      ],
    ];
    
    // Merge all settings
    $settings = array_merge($settings, config());
    
    // Slim parent construct
    parent::__construct($settings);
  }
  
  /**
   * Get instance class
   *
   *
   * @return \Navegarte\App
   */
  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new static;
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
        return call_user_func_array($this->getContainer()->get($id), $param_arr);
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
    return $this->map($methods, $pattern, function (Request $request, Response $response, $params) use ($controller) {
      
      if (strpos($controller, '@') === false) {
        $class = $controller;
        $method = null;
      } else {
        list($class, $method) = explode('@', $controller);
      }
      
      $controller = 'App\\Controllers\\' . str_replace('/', '\\', $class);
      $object = new $controller($request, $response, $params, $this);
      
      if (!method_exists($object, strtolower($request->getMethod()) . ucfirst($method))) {
        throw new \Exception('Method <b>' . get_class($object) . '::' . strtolower($request->getMethod()) . ucfirst($method) . '</b> does not exists!');
      }
  
      return call_user_func_array([$object, strtolower($request->getMethod()) . ucfirst($method),], $params);
    });
  }
  
  /**
   * Register Middleware for application
   *
   * @return mixed
   */
  public function registerMiddleware()
  {
    $registers = $this->getRegisters();
    
    foreach ($registers['middleware'] as $class) {
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
    foreach ($registers['providers'] as $class) {
      if (class_exists($class)) {
  
        /** @var \Navegarte\Contracts\BaseServiceProvider $provider */
        $provider = new $class;
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
    /** @var \Navegarte\App $app */
    $app = $this;
    
    // Router for web
    if (file_exists(ROOT . '/routes/web.php')) {
      $this->group('', function () use ($app) {
        include ROOT . '/routes/web.php';
      });
    }
    
    // Router for api
    if (file_exists(ROOT . '/routes/api.php')) {
      $this->group('/api', function () use ($app) {
        include ROOT . '/routes/api.php';
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
    
    if (file_exists(ROOT . '/bootstrap/registers.php')) {
      $array = include ROOT . '/bootstrap/registers.php';
      
      return $array;
    }
    
    return $array;
  }
}
