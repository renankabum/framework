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

namespace Core\Providers\Hash {

    use Core\Contracts\Provider;

    /**
     * Class BcryptProvider
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class BcryptProvider extends Provider
    {
        /**
         * Registers services on the given container.
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \Core\Providers\Hash\BcryptHasher
             */
            $this->container['hash'] = function () {
                return new BcryptHasher();
            };
        }
    }
}
