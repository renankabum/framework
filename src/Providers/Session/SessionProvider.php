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

namespace Core\Providers\Session {

    use Core\Contracts\Provider;

    /**
     * Class SessionProvider
     *
     * @package Core\Providers\Session
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class SessionProvider extends Provider
    {
        /**
         * Registers services on the given container.
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \Core\Providers\Session\Session
             */
            $this->container['session'] = function () {
                return new Session;
            };

            /**
             * @return \Core\Providers\Session\Flash
             */
            $this->container['flash'] = function () {
                return new Flash;
            };
        }
    }
}
