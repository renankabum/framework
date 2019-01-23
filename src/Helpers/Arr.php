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
     * Class Arr
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Arr extends \Illuminate\Support\Arr
    {
        /**
         * @param array $array
         * @param int   $mode
         *
         * @return int
         */
        public static function count(array $array, $mode = COUNT_NORMAL)
        {
            return count($array, $mode);
        }
    }
}
