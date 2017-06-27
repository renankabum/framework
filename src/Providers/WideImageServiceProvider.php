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

namespace Navegarte\Providers;

use Navegarte\Contracts\ServiceProviderAbstract;
use Slim\Container;
use WideImage\WideImage;

/**
 * Class WideImageServiceProvider
 *
 * @package Navegarte\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class WideImageServiceProvider extends ServiceProviderAbstract
{
    /**
     * Registers service on the given container.
     *
     * Usar: http://prntscr.com/foimz8
     *
     * @param \Slim\Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        /**
         * @return \WideImage\WideImage
         */
        $container['image'] = function () {
            return new WideImage();
        };
    }
}
