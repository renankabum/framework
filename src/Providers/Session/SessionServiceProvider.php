<?php

/**
 * NAVEGARTE Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-${YEAH} Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Providers\Session;

use Navegarte\Contracts\BaseServiceProvider;
use Slim\Container;

/**
 * Class SessionServiceProvider
 *
 * @package Navegarte\Providers\Session
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class SessionServiceProvider extends BaseServiceProvider
{
  
  /**
   * Registers services on the given container.
   *
   * @param \Slim\Container $container
   *
   * @return mixed|void
   */
  public function register(Container $container)
  {
    $container['session'] = function () {
      return new Session;
    };
  }
}
