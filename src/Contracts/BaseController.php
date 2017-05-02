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
 * Class BaseController
 *
 * @package Navegarte\Contracts
 */
abstract class BaseController
{
  /**
   * @var \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request
   */
  protected $request;
  
  /**
   * @var \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
   */
  protected $response;
  
  /**
   * @var string|array
   */
  protected $params;
  
  /**
   * @var \Slim\Container
   */
  protected $container;
  
  /**
   * BaseController constructor.
   *
   * @param \Psr\Http\Message\ServerRequestInterface $request
   * @param \Psr\Http\Message\ResponseInterface      $response
   * @param string|array                             $params
   * @param \Slim\Container                          $container
   */
  public function __construct(Request $request, Response $response, $params, Container $container)
  {
    $this->request = $request;
    $this->response = $response;
    $this->params = $params;
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
}
