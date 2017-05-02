<?php

/**
 * NAVEGARTE Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-${YEAH} Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Providers;

use Navegarte\Contracts\BaseServiceProvider;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ErrorServiceProvider
 *
 * @package Navegarte\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class ErrorServiceProvider extends BaseServiceProvider
{
  
  /**
   * Registers services on the given container.
   *
   * @param \Slim\Container $container
   *
   * @return mixed|void
   */
  public function register(Container $container)
  {
    /**
     * @return \Closure
     */
    $container['phpErrorHandler'] = $container['errorHandler'] = function () use ($container) {
      /**
       * @param \Slim\Http\Request  $request
       * @param \Slim\Http\Response $response
       * @param \Exception          $exception
       *
       * @return mixed
       */
      return function (Request $request, Response $response, \Exception $exception) use ($container) {
        
        if ($request->isXhr()) {
          return $response->withJson([
            'error' => [
              'status' => 500,
              'message' => htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8', false)
            ]
          ], 500);
        }
        
        $data['debug'] = null;
        
        if ($container['settings']['displayErrorDetails']) {
          $data = [
            'debug' => true,
            'error' => [
              'message' => $exception->getMessage(),
              'file' => $exception->getFile(),
              'line' => $exception->getLine(),
              'code' => $exception->getCode(),
            ],
          ];
        }
        
        return view('error/500', $data, 500);
      };
    };
    
    /**
     * @return \Closure
     */
    $container['notFoundHandler'] = function () use ($container) {
      /**
       * @param \Slim\Http\Request  $request
       * @param \Slim\Http\Response $response
       *
       * @return mixed
       */
      return function (Request $request, Response $response) use ($container) {
        return view('error/404', [
          'url' => urldecode($request->getUri())
        ], 404);
      };
    };
    
    /**
     * @return \Closure
     */
    $container['notAllowedHandler'] = function () use ($container) {
      /**
       * @param \Slim\Http\Request  $request
       * @param \Slim\Http\Response $response
       *
       * @return mixed
       */
      return function (Request $request, Response $response, $methods) use ($container) {
        return view('error/405', [
          'url' => urldecode($request->getUri()),
          'method' => $request->getMethod(),
          'methods' => implode(', ', $methods),
        ], 405);
      };
    };
  }
}
