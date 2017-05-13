<?php

/**
 * NAVEGARTE Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace App\Middleware;

use Navegarte\Contracts\BaseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class OldInputMiddleware
 *
 * @package App\Middleware
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class OldInputMiddleware extends BaseMiddleware
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
      $this->view->getEnvironment()->addGlobal('old', $this->session->get('old'));
      $this->session->set('old', $request->getParams());
    }
    $response = $next($request, $response);
    
    return $response;
  }
}
