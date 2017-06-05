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
  
  /**
   * @param $tituloEleitor
   *
   * @return bool
   */
  public static function tituloEleitor($tituloEleitor)
  {
    $te = str_pad(preg_replace('[^0-9]', '', $tituloEleitor), 12, '0', STR_PAD_LEFT);
    $uf = intval(substr($tituloEleitor, 8, 2));
    
    if (strlen($tituloEleitor) != 12 || $uf < 1 || $uf > 28) {
      return false;
    } else {
      $d = 0;
      
      for ($i = 0; $i < 8; $i++) {
        $d += $tituloEleitor{$i} * (9 - $i);
      }
      
      $d %= 11;
      
      if ($d < 2) {
        if ($uf < 3) {
          $d = 1 - $d;
        } else {
          $d = 0;
        }
      } else {
        $d = 11 - $d;
      }
      
      if ($tituloEleitor{10} != $d) {
        return false;
      }
      
      $d *= 2;
      
      for ($i = 8; $i < 10; $i++) {
        $d += $tituloEleitor{$i} * (12 - $i);
      }
      
      $d %= 11;
      
      if ($d < 2) {
        if ($uf < 3) {
          $d = 1 - $d;
        } else {
          $d = 0;
        }
      } else {
        $d = 11 - $d;
      }
      
      if ($tituloEleitor{11} != $d) {
        return false;
      }
      
      return true;
    }
  }
  
  /**
   * Get real ip user
   *
   * @return mixed
   */
  public static function getIp()
  {
    static::$data = '';
    if (getenv('HTTP_CLIENT_IP')) {
      static::$data = getenv('HTTP_CLIENT_IP');
    } else if (getenv('HTTP_X_FORWARDED_FOR')) {
      static::$data = getenv('HTTP_X_FORWARDED_FOR');
    } else if (getenv('HTTP_X_FORWARDED')) {
      static::$data = getenv('HTTP_X_FORWARDED');
    } else if (getenv('HTTP_FORWARDED_FOR')) {
      static::$data = getenv('HTTP_FORWARDED_FOR');
    } else if (getenv('HTTP_FORWARDED')) {
      static::$data = getenv('HTTP_FORWARDED');
    } else if (getenv('REMOTE_ADDR')) {
      static::$data = getenv('REMOTE_ADDR');
    } else {
      static::$data = $_SERVER['REMOTE_ADDR'];
    }
    
    return static::$data;
  }
}
