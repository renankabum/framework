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

namespace Navegarte\Providers\View;

use Navegarte\Contracts\BaseServiceProvider;
use Slim\Container;

/**
 * Class ViewServiceProvider
 *
 * @package Navegarte\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class ViewServiceProvider extends BaseServiceProvider
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
    $container['view'] = function () use ($container) {
      
      if (config('view.engine') === 'php') {
        return 'PHP';
      }
      
      if (config('view.engine') === 'blade') {
        return (new Blade($container))->register();
      }
      
      if (config('view.engine') === 'twig') {
        return (new Twig($container))->register();
      }
      
      throw new \Exception('A Camada [view] está configurada incorretamente. Favor verificar suas configurações!', E_USER_NOTICE);
    };
  }
}
