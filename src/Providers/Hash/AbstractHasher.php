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
    
    /**
     * Class AbstractHasher
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class AbstractHasher
    {
        /**
         * Retorna a informações sobre o hash fornecido
         *
         * @param string $hashedValue
         *
         * @return array
         */
        public function info($hashedValue)
        {
            return password_get_info($hashedValue);
        }
        
        /**
         * Verifica se o hash fornecido corresponde com o password fornecido.
         *
         * @param string $value
         * @param string $hashedValue
         *
         * @return boolean
         */
        public function check($value, $hashedValue)
        {
            if (strlen($hashedValue) === 0) {
                return false;
            }
            
            return password_verify($value, $hashedValue);
        }
    }
}
