<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-${YEAH} Vagner Cardoso - Core
 */

namespace Core\Providers\Session;

use Core\Contracts\ServiceProviderAbstract;
use Slim\Container;

/**
 * Class SessionServiceProvider
 *
 * @package Core\Providers\Session
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class SessionServiceProvider extends ServiceProviderAbstract
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
         * @return \Core\Providers\Session\Session
         */
        $container['session'] = function () {
            return new Session();
        };
    }
}
