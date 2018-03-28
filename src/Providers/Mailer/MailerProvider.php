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

namespace Core\Providers\Mailer {
    
    use Core\Contracts\Provider;
    
    /**
     * Class MailerProvider
     *
     * @package Core\Providers\Mailer
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class MailerProvider extends Provider
    {
        /**
         * Registers services on the given container.
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \Core\Providers\Mailer\Mailer
             * @throws \phpmailerException
             */
            $this->container['mailer'] = function () {
                return new Mailer($this->container);
            };
        }
    }
}
