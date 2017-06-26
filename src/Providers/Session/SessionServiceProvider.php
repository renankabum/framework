<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-${YEAH} Vagner Cardoso - VCWeb
 */

namespace Navegarte\Providers\Session;

use Navegarte\Contracts\ServiceProviderAbstract;
use Slim\Container;

/**
 * Class SessionServiceProvider
 *
 * @package Navegarte\Providers\Session
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
         * @return \Navegarte\Providers\Session\Session
         */
        $container['session'] = function () {
            return new Session();
        };
    }
}
