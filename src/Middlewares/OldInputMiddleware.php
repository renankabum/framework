<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 15/04/2019 Vagner Cardoso
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
         * @param \Slim\Http\Request $request PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable $next Next middleware
         *
         * @return \Slim\Http\Response
         */
        public function __invoke(Request $request, Response $response, callable $next)
        {
            if (!$request->isXhr()) {
                // Adiciona o parametro na global na view
                $this->view->addGlobal('oldInput', ($this->session ? $this->session->get('oldInput') : []));
                
                // Verifica se está ativa a sessão
                // e adiciona o parametro
                if ($this->session) {
                    $this->session->set('oldInput', params());
                }
            }
            
            $response = $next($request, $response);
            
            return $response;
        }
    }
}
