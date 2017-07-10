<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Middleware;

use Core\Contracts\MiddlewareAbstract;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class OldInputMiddleware
 *
 * @package Core\Middleware
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class OldInputMiddleware extends MiddlewareAbstract
{
    /**
     * Register OldInput middleware
     *
     * @param \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response     $response PSR7 response
     * @param callable                                                    $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$request->isXhr()) {
            $this->view->addGlobal('old', $this->session->get('old'));
            $this->session->set('old', $request->getParams());
        }
        
        $response = $next($request, $response);
        
        return $response;
    }
}
