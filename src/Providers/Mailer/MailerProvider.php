<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Providers\Mailer {
    
    use Core\Contracts\Provider as BaseProvider;
    
    /**
     * Class MailerProvider
     *
     * @package Core\Providers\Mailer
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class MailerProvider extends BaseProvider
    {
        /**
         * Registra o serviço de envio de e-mail
         *
         * @return void
         */
        public function register()
        {
            $this->container['mailer'] = new Mailer();
        }
    }
}
