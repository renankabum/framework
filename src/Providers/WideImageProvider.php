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
    use WideImage\WideImage;

    /**
     * Class WideImageProvider
     *
     * composer require smottt/wideimage
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class WideImageProvider extends Provider
    {
        /**
         * Registers service on the given container.
         *
         * Usar: http://prntscr.com/foimz8
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \WideImage\WideImage
             */
            $container['image'] = function () {
                return new WideImage();
            };
        }
    }
}
