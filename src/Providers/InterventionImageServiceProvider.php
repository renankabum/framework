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
use Intervention\Image\ImageManager;
use Slim\Container;

/**
 * Class InterventionImageServiceProvider
 *
 * @package Core\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class InterventionImageServiceProvider extends ServiceProviderAbstract
{
    /**
     * Registers service on the given container.
     *
     * @param \Slim\Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        /**
         * @return \Intervention\Image\ImageManager
         */
        $container['image'] = function () {
            
            return new ImageManager(['driver' => 'gd']);
        };
    }
}
