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
     * Class MaintenanceMiddleware
     *
     * @package Core\Middlewares
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class MaintenanceMiddleware extends Middleware
    {
        /**
         * Registra middleware para verificar se a aplicação está em manutenção
         *
         * @param \Slim\Http\Request $request PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable $next Next middleware
         *
         * @return \Slim\Http\Response
         * @throws \Exception
         */
        public function __invoke(Request $request, Response $response, callable $next)
        {
            // Verifica se a manutenção está ativa
            if (env('APP_MAINTENANCE', false) === true) {
                return view('@error.503', [], 503);
            }
            
            $response = $next($request, $response);
            
            return $response;
        }
    }
}
