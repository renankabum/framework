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

namespace Core\Providers;

use Core\Contracts\ServiceProviderAbstract;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ErrorServiceProvider
 *
 * @package Core\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class ErrorServiceProvider extends ServiceProviderAbstract
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
                $array = [];
                
                if ($request->isXhr()) {
                    $array = [
                        'error' => [
                            'status' => 500,
                            'message' => htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8', false),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine()
                        ]
                    ];
                    
                    return $response->withJson($array, 500);
                }
                
                $array['debug'] = null;
                
                if ($container['settings']['displayErrorDetails']) {
                    $array = [
                        'debug' => true,
                        'error' => [
                            'message' => $exception->getMessage(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'code' => $exception->getCode(),
                        ],
                    ];
                }
                
                return view('error/500', $array, 500);
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
                $array = [
                    'url' => urldecode($request->getUri())
                ];
                
                return view('error/404', $array, 404);
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
                $array = [
                    'url' => urldecode($request->getUri()),
                    'method' => $request->getMethod(),
                    'methods' => implode(', ', $methods)
                ];
                
                return view('error/405', $array, 405);
            };
        };
    }
}
