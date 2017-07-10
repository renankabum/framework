<?php
/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Providers\Mailer;

use Core\Contracts\ServiceProviderAbstract;
use Slim\Container;

/**
 * Class MailerServiceProvider
 *
 * @package Core\Providers\Mailer
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
         * @return \Core\Providers\Mailer\Mailer
         */
        $container['mailer'] = function () use ($container) {
            
            return new Mailer($container);
        };
    }
}
