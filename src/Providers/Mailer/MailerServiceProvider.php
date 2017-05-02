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

namespace Navegarte\Providers\Mailer;

use Navegarte\Contracts\BaseServiceProvider;
use Slim\Container;

/**
 * Class MailerServiceProvider
 *
 * @package Navegarte\Providers\Mailer
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class MailerServiceProvider extends BaseServiceProvider
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
    $container['mailer'] = function () use ($container) {
      
      return 'mailer';
    };
  }
}
