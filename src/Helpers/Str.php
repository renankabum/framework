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
     * Class Str
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Str extends \Illuminate\Support\Str
    {
        /**
         * @param string $string
         * @param int    $limit
         * @param string $end
         *
         * @return string
         */
        public static function chars($string, $limit = 50, $end = '...')
        {
            if (strlen($string) <= $limit) {
                return $string;
            }

            return self::substr($string, 0, strrpos(self::substr($string, 0, $limit), ' ')) . $end;
        }
    }
}
