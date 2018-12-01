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
    class ErrorProvider extends Provider
    {
        /**
         * Modifica o padrão de exibição de erros na aplicação
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
                    /** @var \Slim\Route $route */
                    $route = $request->getAttribute('route');
                    
                    $errors = [
                        'debug' => $this->container->settings['displayErrorDetails'],
                        'error' => [
                            'code' => $exception->getCode(),
                            'file' => str_replace([PUBLIC_FOLDER, APP_FOLDER, RESOURCE_FOLDER], '', $exception->getFile()),
                            'line' => $exception->getLine(),
                            'message' => $exception->getMessage(),
                            'route' => is_object($route) ? "(".implode(", ", $route->getMethods()).") {$route->getPattern()}" : null,
                            'trace' => explode("\n", $exception->getTraceAsString()),
                        ],
                    ];
                    
                    // Verifica se é ajax ou api
                    if (is_php_cli() || ($request->isXhr() || has_route('/api'))) {
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
                    
                    // Verifica se é ajax ou api
                    if (is_php_cli() || ($request->isXhr() || has_route('/api'))) {
                        return $response->withJson([
                            'error' => [
                                'url' => $uri,
                                'message' => 'Error 404 (Not Found)',
                            ],
                        ], 404);
                    }
                    
                    return view('error.404', ['url' => $uri], 404);
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
                    
                    // Verifica se é ajax ou api
                    if (is_php_cli() || ($request->isXhr() || has_route('/api'))) {
                        return $response->withJson([
                            'error' => [
                                'url' => $uri,
                                'method' => $method,
                                'methods' => implode(', ', $methods),
                                'message' => 'Error 405 (Method not Allowed)',
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
