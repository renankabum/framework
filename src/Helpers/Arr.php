<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Helpers;

/**
 * Class Arr
 *
 * @package Core\Helpers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Arr extends \Illuminate\Support\Arr
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
