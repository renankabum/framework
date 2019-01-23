<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 28/04/2017 Vagner Cardoso
 */

namespace Core\Middlewares {
    
    use Core\Contracts\Middleware;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class OldInputMiddleware
     *
     * @package Core\Middlewares
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class OldInputMiddleware extends Middleware
    {
        /**
         * Registra middleware para guardar o parsedBody na view
         *
         * @param \Slim\Http\Request  $request  PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable            $next     Next middleware
         *
         * @return \Slim\Http\Response
         */
        public function __invoke(Request $request, Response $response, callable $next)
        {
            if (!$request->isXhr()) {
                $this->view->addGlobal('oldInput', (!empty(params())) ? params() : '');
            }
            
            $response = $next($request, $response);
            
            return $response;
        }
    }
}
