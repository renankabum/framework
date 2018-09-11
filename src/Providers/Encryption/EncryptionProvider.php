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
            // Configurações de criptografia
            $encryption = config('app.encryption');
            
            $this->container['encryption'] = new Encryption($encryption['key'], $encryption['cipher']);
        }
    }
}
