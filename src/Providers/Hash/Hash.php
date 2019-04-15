<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 15/04/2019 Vagner Cardoso
 */

namespace Core\Providers\Hash {
    
    /**
     * Class Hash
     *
     * @package Core\Contracts
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Hash
    {
        /**
         * @param string $hashedValue
         *
         * @return array
         */
        public function info($hashedValue)
        {
            return password_get_info(
                $hashedValue
            );
        }
        
        /**
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
            
            return password_verify(
                $value, $hashedValue
            );
        }
    }
}
