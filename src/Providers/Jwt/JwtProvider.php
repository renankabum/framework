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

namespace Core\Providers\Jwt {
    
    use Core\Contracts\Provider;
    
    /**
     * Class JwtProvider
     *
     * @package Core\Providers\Jwt
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class JwtProvider extends Provider
    {
        /**
         * Registra serviço para gerar o JWT (Json Web Token)
         *
         * @return void
         */
        public function register()
        {
            $this->container['jwt'] = function () {
                $key = (config('app.encryption.key', null) ?: md5(md5('VCWEBNETWORKS')));
                
                return new Jwt($key);
            };
        }
    }
}