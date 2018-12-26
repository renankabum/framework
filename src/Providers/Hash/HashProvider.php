<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Providers\Hash {
    
    use Core\Contracts\Provider;
    
    /**
     * Class HashProvider
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class HashProvider extends Provider
    {
        /**
         * Registra os serviço de criptografia das senhas
         *
         * @return void
         */
        public function register()
        {
            $this->container['hash'] = function () {
                $driver = env('APP_HASH_DRIVER', 'bcrypt');
                
                switch ($driver) {
                    case 'bcrypt':
                        return new Bcrypt();
                        break;
                    
                    case 'argon':
                        return new Argon();
                        break;
                }
            };
        }
    }
}
