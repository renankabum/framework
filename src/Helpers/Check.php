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

/**
 * Class Check
 *
 * @package Navegarte\Helpers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Check
{
  /**
   * @var mixed
   */
  private static $data;
  
  /**
   * @var mixed
   */
  private static $format;
  
  /**
   * Verifica se o e-Mail está em um formato válido!
   *
   * @param $email
   *
   * @return bool
   */
  public static function email($email)
  {
    static::$data = (string)$email;
    static::$format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';
    
    if (!filter_var(static::$data, FILTER_VALIDATE_EMAIL) === false && preg_match(static::$format, static::$data)) {
      return true;
    }
    
    return false;
  }
}
