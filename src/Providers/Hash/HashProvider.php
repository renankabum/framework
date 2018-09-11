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

namespace Core\Providers\Hash {
    
    use Core\Contracts\Provider as BaseProvider;
    
    /**
     * Class BcryptProvider
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class BcryptProvider extends HashProvider
    {
    }
    
    /**
     * Class HashProvider
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class HashProvider extends BaseProvider
    {
        /**
         * Registra os serviço de criptografia das senhas
         *
         * @return void
         */
        public function register()
        {
            // Bcrypt
            $this->container['hash'] = function () {
                return new Bcrypt();
            };
            
            // Argon 2I
            $this->container['argon'] = function () {
                return new Argon();
            };
        }
    }
}
