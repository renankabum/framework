<?php

/**
 * NAVEGARTE Networks
 *
 * @package   FrontEnd
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Helpers;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Class DumpHelper
 *
 * @package Navegarte\Helpers
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
      $dump = (PHP_SAPI == 'cli' ? new CliDumper : new HtmlDumper);
      $dump->dump((new VarCloner)->cloneVar($var));
    } else {
      var_dump($var);
    }
  }
}
