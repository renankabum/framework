<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 28/04/2017 Vagner Cardoso
 */

namespace Core\Providers\Encryption {
    
    use Core\Contracts\Provider as BaseProvider;
    
    /**
     * Class EncryptionProvider
     *
     * @package Core\Providers\Encryption
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class EncryptionProvider extends BaseProvider
    {
        /**
         * Registra o serviço de criptografia de dados
         *
         * @return void
         */
        public function register()
        {
            $this->container['encryption'] = function () {
                return new Encryption((env('APP_KEY') ?: md5(md5('VCWEBNETWORKS'))));
            };
        }
    }
}
