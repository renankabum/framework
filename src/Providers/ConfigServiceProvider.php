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

namespace Core\Providers;

use Core\Contracts\ServiceProviderAbstract;
use Core\Helpers\Config;
use Slim\Container;

/**
 * Class ConfigServiceProvider
 *
 * @package Core\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class ConfigServiceProvider extends ServiceProviderAbstract
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
        $items = config();
        
        /**
         * @return \Core\Helpers\Config
         */
        $container['config'] = function () use ($items) {
            return new Config($items);
        };
    }
}
