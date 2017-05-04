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

namespace Navegarte\Providers\View\Contracts;

use Slim\Container;

/**
 * Class BaseView
 *
 * @package Navegarte\Providers\View\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class BaseView
{
  /**
   * @var \Slim\Container $container
   */
  protected $container;
  
  /**
   * Twig constructor.
   *
   * @param \Slim\Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }
  
  abstract public function register();
  
  /**
   * Get provider
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
