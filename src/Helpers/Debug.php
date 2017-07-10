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

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Class DumpHelper
 *
 * @package Core\Helpers
 */
final class Debug
{
    /**
     * Symfony customize dump
     *
     * @param $var
     */
    public function dump($var)
    {
        if (class_exists(CliDumper::class)) {
            $dump = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper() : new HtmlDumper();
            $dump->dump((new VarCloner())->cloneVar($var));
        } else {
            var_dump($var);
        }
    }
}
