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

namespace Navegarte\Providers\Hash;

use Navegarte\Contracts\BaseServiceProvider;
use Slim\Container;

/**
 * Class BcryptServiceProvider
 *
 * @package Navegarte\Providers\Hash
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class BcryptServiceProvider extends BaseServiceProvider
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
    $container['hash'] = function () {
      return new BcryptHasher;
    };
  }
}
