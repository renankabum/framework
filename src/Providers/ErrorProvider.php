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
                    $array = [];

                    if ($request->isXhr()) {
                        $array = [
                            'error' => [
                                'status' => 500,
                                #'message' => htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8', false),
                                'message' => $exception->getMessage(),
                                'file' => $exception->getFile(),
                                'line' => $exception->getLine()
                            ]
                        ];

                        return $response->withJson($array, 500);
                    }

                    $array['debug'] = null;

                    if ($this->container['settings']['displayErrorDetails']) {
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
            $this->container['notFoundHandler'] = function () {
                /**
                 * @param \Slim\Http\Request  $request
                 * @param \Slim\Http\Response $response
                 *
                 * @return mixed
                 */
                return function (Request $request, Response $response) {
                    $array = [
                        'url' => urldecode($request->getUri())
                    ];

                    return view('error/404', $array, 404);
                };
            };

            /**
             * @return \Closure
             */
            $this->container['notAllowedHandler'] = function () {
                /**
                 * @param \Slim\Http\Request  $request
                 * @param \Slim\Http\Response $response
                 * @param                     $methods
                 *
                 * @return mixed
                 */
                return function (Request $request, Response $response, $methods) {
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
}
