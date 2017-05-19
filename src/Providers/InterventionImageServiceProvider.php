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

namespace Navegarte\Providers;

use Intervention\Image\ImageManager;
use Navegarte\Contracts\BaseServiceProvider;
use Slim\Container;

class InterventionImageServiceProvider extends BaseServiceProvider
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
    $container['image'] = function () {
      
      return new ImageManager(['driver' => 'gd']);
    };
  }
}
