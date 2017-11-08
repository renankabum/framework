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

namespace Core\Providers {

    use Core\Contracts\Provider;
    use Intervention\Image\ImageManager;

    /**
     * Class InterventionImageProvider
     *
     * composer require intervention/image
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class InterventionImageProvider extends Provider
    {
        /**
         * Registers service on the given container.
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \Intervention\Image\ImageManager
             */
            $container['image'] = function () {

                return new ImageManager(['driver' => 'gd']);
            };
        }
    }
}
