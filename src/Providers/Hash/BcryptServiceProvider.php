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

namespace Core\Providers\Hash;

use Core\Contracts\ServiceProviderAbstract;
use Slim\Container;

/**
 * Class BcryptServiceProvider
 *
 * @package Core\Providers\Hash
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
         * @return \Core\Providers\Hash\BcryptHasher
         */
        $container['hash'] = function () {
            return new BcryptHasher();
        };
    }
}
