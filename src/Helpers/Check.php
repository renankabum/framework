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

namespace Core\Helpers;

/**
 * Class Check
 *
 * @package Core\Helpers
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
     * Checks if e-Mail is in a valid format!
     *
     * @param string $email
     *
     * @return bool
     */
    public static function email($email)
    {
        static::$data = (string) $email;
        static::$format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';
        
        if (!filter_var(static::$data, FILTER_VALIDATE_EMAIL) === false && preg_match(static::$format, static::$data)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * <b>Checa CPF:</b> Informe um CPF para checar sua validade via algoritmo!
     *
     * @param string $cpf = CPF com ou sem pontuação
     *
     * @return bool = True se for um CPF válido
     */
    public static function cpf($cpf)
    {
        self::$data = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen(self::$data) != 11) {
            return false;
        }
        
        $digitoA = 0;
        $digitoB = 0;
        
        for ($i = 0, $x = 10; $i <= 8; $i++, $x--) {
            $digitoA += self::$data[$i] * $x;
        }
        
        for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
            if (str_repeat($i, 11) == self::$data) {
                return false;
            }
            $digitoB += self::$data[$i] * $x;
        }
        
        $somaA = (($digitoA % 11) < 2) ? 0 : 11 - ($digitoA % 11);
        $somaB = (($digitoB % 11) < 2) ? 0 : 11 - ($digitoB % 11);
        
        if ($somaA != self::$data[9] || $somaB != self::$data[10]) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Checks if the title is in a valid format!
     *
     * @param string $te
     *
     * @return bool
     */
    public static function tituloEleitor($te)
    {
        $te = str_pad(preg_replace('[^0-9]', '', $te), 12, '0', STR_PAD_LEFT);
        $uf = intval(substr($te, 8, 2));
        
        if (strlen($te) != 12 || $uf < 1 || $uf > 28) {
            return false;
        } else {
            $d = 0;
            
            for ($i = 0; $i < 8; $i++) {
                $d += $te{$i} * (9 - $i);
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
            
            if ($te{10} != $d) {
                return false;
            }
            
            $d *= 2;
            
            for ($i = 8; $i < 10; $i++) {
                $d += $te{$i} * (12 - $i);
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
            
            if ($te{11} != $d) {
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
    public static function ip()
    {
        static::$data = '';
        if (getenv('HTTP_CLIENT_IP')) {
            static::$data = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            static::$data = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            static::$data = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            static::$data = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            static::$data = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            static::$data = getenv('REMOTE_ADDR');
        } else {
            static::$data = $_SERVER['REMOTE_ADDR'];
        }
        
        return static::$data;
    }
}
