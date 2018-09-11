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
            return Helper::base64Encode($value);
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
            return Helper::base64Decode($encoded);
        }
    }
}
