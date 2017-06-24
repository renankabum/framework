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

namespace Navegarte\Providers\Mailer;

use Navegarte\Contracts\ServiceProviderAbstract;
use Slim\Container;

/**
 * Class MailerServiceProvider
 *
 * @package Navegarte\Providers\Mailer
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class MailerServiceProvider extends ServiceProviderAbstract
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
      /**
       * @return \Navegarte\Providers\Mailer\Mailer
       */
    $container['mailer'] = function () use ($container) {
    
        return new Mailer($container);
    };
  }
}
