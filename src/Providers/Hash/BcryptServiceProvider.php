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

namespace Navegarte\Providers\Hash;

use Navegarte\Contracts\ServiceProviderAbstract;
use Slim\Container;

/**
 * Class BcryptServiceProvider
 *
 * @package Navegarte\Providers\Hash
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class BcryptServiceProvider extends ServiceProviderAbstract
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
         * @return \Navegarte\Providers\Hash\BcryptHasher
         */
        $container['hash'] = function () {
            return new BcryptHasher();
        };
    }
}
