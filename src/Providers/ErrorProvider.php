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

namespace Core\Providers {
    
    use Core\Contracts\Provider;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class ErrorProvider
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class ErrorProvider extends Provider
    {
        /**
         * Registers services on the given container.
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \Closure
             */
            $this->container['phpErrorHandler'] = $this->container['errorHandler'] = function () {
                /**
                 * @param \Slim\Http\Request  $request
                 * @param \Slim\Http\Response $response
                 * @param \Exception          $exception
                 *
                 * @return mixed
                 */
                return function (Request $request, Response $response, $exception) {
                    $errors = [
                        'debug' => $this->container->settings['displayErrorDetails'],
                        'error' => [
                            'type' => get_class($exception),
                            'code' => $exception->getCode(),
                            'message' => $exception->getMessage(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'trace' => explode("\n", $exception->getTraceAsString()),
                        ],
                    ];
                    
                    if ($request->isXhr()) {
                        return $response->withJson($errors, 500);
                    }
                    
                    return view('error.500', $errors, 500);
                };
            };
            
            /**
             * @return \Closure
             */
            $this->container['notFoundHandler'] = function () {
                /**
                 * @param \Slim\Http\Request  $request
                 * @param \Slim\Http\Response $response
                 *
                 * @return mixed
                 */
                return function (Request $request, Response $response) {
                    $uri = urldecode($request->getUri());
                    
                    if ($request->isXhr()) {
                        return $response->withJson([
                            'error' => [
                                'message' => 'Error 404 (Not Found)',
                                'url' => $uri,
                            ],
                        ], 404);
                    }
                    
                    return view('error.404', [
                        'url' => $uri,
                    ], 404);
                };
            };
            
            /**
             * @return \Closure
             */
            $this->container['notAllowedHandler'] = function () {
                /**
                 * @param \Slim\Http\Request  $request
                 * @param \Slim\Http\Response $response
                 * @param string[]            $methods
                 *
                 * @return mixed
                 */
                return function (Request $request, Response $response, $methods) {
                    $uri = urldecode($request->getUri());
                    $method = $request->getMethod();
                    
                    if ($request->isXhr()) {
                        return $response->withJson([
                            'error' => [
                                'message' => 'Error 405 (Method not Allowed)',
                                'url' => $uri,
                                'method' => $method,
                                'methods' => implode(', ', $methods),
                            ],
                        ], 405);
                    }
                    
                    return view('error.405', [
                        'url' => $uri,
                        'method' => $method,
                        'methods' => implode(', ', $methods),
                    ], 405);
                };
            };
        }
    }
}
