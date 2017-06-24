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

namespace Navegarte\Middleware;

use Navegarte\Contracts\MiddlewareAbstract;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class TrailingSlashMiddleware
 *
 * @package Navegarte\Middleware
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class TrailingSlashMiddleware extends MiddlewareAbstract
{
  /**
   * Register middleware
   *
   * @param \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request $request  PSR7 request
   * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response     $response PSR7 response
   * @param callable                                                    $next     Next middleware
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function __invoke(Request $request, Response $response, callable $next)
  {
    $uri = $request->getUri();
    $path = $uri->getPath();
    
    if ($path != '/' && substr($path, -1) == '/') {
      $uri = $uri->withPath(substr($path, 0, -1));
      
      if ($request->getMethod() == 'GET') {
        return $response->withRedirect((string)$uri, 301);
      } else {
        $response = $next($request->withUri($uri), $response);
        
        return $response;
      }
    }
    
    $response = $next($request, $response);
    
    return $response;
  }
}
