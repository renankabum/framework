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

namespace Core\Helpers {
    
    /**
     * Class Base64
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Base64
    {
        /**
         * Encode base64 URL-SAFE
         *
         * @param string $value
         *
         * @return string
         */
        public static function encode($value)
        {
            return str_replace(
                '=', '', strtr(
                    base64_encode($value), '+/', '-_'
                )
            );
        }
        
        /**
         * Decode base64 URL-SAFE
         *
         * @param string $encoded
         *
         * @return bool|string
         */
        public static function decode($encoded)
        {
            $remainder = strlen($encoded) % 4;
            
            if ($remainder) {
                $padlen = 4 - $remainder;
                $encoded .= str_repeat('=', $padlen);
            }
            
            return base64_decode(
                strtr($encoded, '-_', '+/')
            );
        }
    }
}
