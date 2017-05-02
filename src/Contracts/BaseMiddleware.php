<?php

/**
 * NAVEGARTE Networks
 *
 * @package   FrontEnd
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Contracts;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;

/**
 * Class BaseMiddleware
 *
 * @package Navegarte\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class BaseMiddleware
{
  /**
   * @var \Slim\Container
   */
  protected $container;
  
  /**
   * BaseMiddleware constructor.
   *
   * @param \Slim\Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }
  
  /**
   * Get property in container
   *
   * @param $name
   *
   * @return mixed
   */
  public function __get($name)
  {
    if ($this->container->{$name}) {
      return $this->container->{$name};
    }
  }
  
  /**
   * Register middleware
   *
   * @param \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request $request  PSR7 request
   * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response     $response PSR7 response
   * @param callable                                                    $next     Next middleware
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  abstract public function __invoke(Request $request, Response $response, callable $next);
}
