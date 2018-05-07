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
     * Class Check
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Check
    {
        /**
         * <b>email:</b>
         * Verifica se o EMAIL é válido.
         *
         * @param string $email
         *
         * @return boolean
         */
        public static function email($email)
        {
            $email = filter_var((string)$email, FILTER_SANITIZE_EMAIL);
            $regex = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match($regex, $email)) {
                return true;
            }
            
            return false;
        }
        
        /**
         * <b>cpf:</b>
         * Verifica se o CPF é válido.
         *
         * @param string $cpf
         *
         * @return bool
         */
        public static function cpf($cpf)
        {
            $cpf = preg_replace('/[^0-9]/', '', $cpf);
            
            if (strlen($cpf) != 11) {
                return false;
            }
            
            $digitoA = 0;
            $digitoB = 0;
            
            for ($i = 0, $x = 10; $i <= 8; $i++, $x--) {
                $digitoA += $cpf[$i] * $x;
            }
            
            for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
                if (str_repeat($i, 11) == $cpf) {
                    return false;
                }
                
                $digitoB += $cpf[$i] * $x;
            }
            
            $somaA = (($digitoA % 11) < 2) ? 0 : 11 - ($digitoA % 11);
            $somaB = (($digitoB % 11) < 2) ? 0 : 11 - ($digitoB % 11);
            
            if ($somaA != $cpf[9] || $somaB != $cpf[10]) {
                return false;
            } else {
                return true;
            }
        }
        
        /**
         * <b>cnpj:</b>
         * Verifica se o CNPJ é válido.
         *
         * @param string $cnpj
         *
         * @return bool
         */
        public static function cnpj($cnpj)
        {
            $cnpj = (string)$cnpj;
            $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
            
            if (strlen($cnpj) != 14) {
                return false;
            }
            
            $A = 0;
            $B = 0;
            
            for ($i = 0, $c = 5; $i <= 11; $i++, $c--) {
                $c = ($c == 1 ? 9 : $c);
                $A += $cnpj[$i] * $c;
            }
            
            for ($i = 0, $c = 6; $i <= 12; $i++, $c--) {
                if (str_repeat($i, 14) == $cnpj) {
                    return false;
                }
                
                $c = ($c == 1 ? 9 : $c);
                $B += $cnpj[$i] * $c;
            }
            
            $somaA = (($A % 11) < 2) ? 0 : 11 - ($A % 11);
            $somaB = (($B % 11) < 2) ? 0 : 11 - ($B % 11);
            
            if (strlen($cnpj) != 14) {
                return false;
            } else if ($somaA != $cnpj[12] || $somaB != $cnpj[13]) {
                return false;
            } else {
                return true;
            }
        }
        
        /**
         * <b>tituloEleitor:</b>
         * Verifica se o TITULO do ELEITOR é válido.
         *
         * @param string $titulo
         *
         * @return bool
         */
        public static function tituloEleitor($titulo)
        {
            $titulo = str_pad(preg_replace('[^0-9]', '', $titulo), 12, '0', STR_PAD_LEFT);
            $uf = intval(substr($titulo, 8, 2));
            
            if (strlen($titulo) != 12 || $uf < 1 || $uf > 28) {
                return false;
            } else {
                $d = 0;
                
                for ($i = 0; $i < 8; $i++) {
                    $d += $titulo{$i} * (9 - $i);
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
                
                if ($titulo{10} != $d) {
                    return false;
                }
                
                $d *= 2;
                
                for ($i = 8; $i < 10; $i++) {
                    $d += $titulo{$i} * (12 - $i);
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
                
                if ($titulo{11} != $d) {
                    return false;
                }
                
                return true;
            }
        }
        
        /**
         * <b>ip:</b>
         * Recupera o IP Address do acesso ao sistema
         *
         * @return string
         */
        public static function ip()
        {
            if (getenv('HTTP_CLIENT_IP')) {
                $realIp = getenv('HTTP_CLIENT_IP');
            } else if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realIp = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_X_FORWARDED')) {
                $realIp = getenv('HTTP_X_FORWARDED');
            } else if (getenv('HTTP_FORWARDED_FOR')) {
                $realIp = getenv('HTTP_FORWARDED_FOR');
            } else if (getenv('HTTP_FORWARDED')) {
                $realIp = getenv('HTTP_FORWARDED');
            } else if (getenv('REMOTE_ADDR')) {
                $realIp = getenv('REMOTE_ADDR');
            } else {
                $realIp = $_SERVER['REMOTE_ADDR'];
            }
            
            if (mb_strpos($realIp, ',') !== false) {
                $ip = explode(',', $realIp);
                
                $realIp = $ip[0];
            }
            
            return $realIp;
        }
        
        /**
         * <b>userAgent:</b>
         * Recupera as informações do dispositivo que está acessando o sistema.
         *
         * @return array
         */
        public static function userAgent()
        {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            
            // Pegando informacoes do navegador
            if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched)) {
                $browser_version = $matched[1];
                $browser = 'IE';
            } else if (preg_match('|Opera/([0-9].[0-9]{1,2})|', $useragent, $matched)) {
                $browser_version = $matched[1];
                $browser = 'Opera';
            } else if (preg_match('|Firefox/([0-9\.]+)|', $useragent, $matched)) {
                $browser_version = $matched[1];
                $browser = 'Firefox';
            } else if (preg_match('|Chrome/([0-9\.]+)|', $useragent, $matched)) {
                $browser_version = $matched[1];
                $browser = 'Chrome';
            } else if (preg_match('|Safari/([0-9\.]+)|', $useragent, $matched)) {
                $browser_version = $matched[1];
                $browser = 'Safari';
            } else {
                $browser_version = 0;
                $browser = 'Outro';
            }
            
            // Pegando informacoes do OS
            if (preg_match('|Mac|', $useragent, $matched)) {
                $so = 'MAC';
            } else if (preg_match('|Windows|', $useragent, $matched) || preg_match('|WinNT|', $useragent, $matched) || preg_match('|Win95|', $useragent, $matched)) {
                $so = 'Windows';
            } else if (preg_match('|Linux|', $useragent, $matched)) {
                $so = 'Linux';
            } else {
                $so = 'Outro';
            }
            
            return array(
                'browser' => $browser,
                'version' => $browser_version,
                'so' => $so,
                'user_agent' => $useragent,
            );
        }
        
        /**
         * <b>isMobile:</b>
         * Verifica se o dispositivo acessando o sistema é um celular/tablet.
         *
         * @return bool
         */
        public static function isMobile()
        {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            
            // Detecta se está em CELULAR
            if (!empty($useragent) && preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis', $useragent)) {
                return true;
            } else {
                return false;
            }
        }
    }
}
