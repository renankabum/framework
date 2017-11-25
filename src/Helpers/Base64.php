<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Helpers {

    /**
     * Class Base64
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Base64
    {
        /**
         * Encoda o valor passado
         *
         * @param string|int $value
         *
         * @return mixed
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
         * Decoda o valor passado como encode
         *
         * @param string $value
         *
         * @return bool|string
         */
        public static function decode($value)
        {
            $remainder = strlen($value) % 4;

            if ($remainder) {
                $padlen = 4 - $remainder;
                $value .= str_repeat('=', $padlen);
            }

            return base64_decode(
                strtr($value, '-/', '+/')
            );
        }
    }
}
