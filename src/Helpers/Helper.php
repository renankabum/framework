<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 15/04/2019 Vagner Cardoso
 */

namespace Core\Helpers {
    
    /**
     * Class Check
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Check extends Helper
    {
        /**
         * Verifica os métodos antigo da classe e converte para o novo
         *
         * @param string $method
         * @param mixed $parameters
         *
         * @return array|bool|string
         */
        public static function __callStatic($method, $parameters)
        {
            $parameter = null;
            
            if (!empty($parameters[0])) {
                $parameter = $parameters[0];
            }
            
            switch ($method) {
                case 'email':
                    return static::checkMail($parameter);
                    break;
                case 'cpf':
                    return static::checkCpf($parameter);
                    break;
                case 'cnpj':
                    return static::checkCnpj($parameter);
                    break;
                case 'tituloEleitor':
                    return static::checkTitleVoter($parameter);
                    break;
                case 'ip':
                    return static::getIpAddress();
                    break;
                case 'userAgent':
                    return static::getUserAgent();
                    break;
                case 'isMobile':
                    return static::checkMobile();
                    break;
            }
            
            if (!method_exists(get_class(), $method)) {
                throw new \BadMethodCallException(sprintf("Call to undefined method %s::%s()", get_class(), $method), E_ERROR);
            }
        }
    }
    
    /**
     * Class Helper
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Helper
    {
        /**
         * Verifica se o EMAIL é válido.
         *
         * @param string $email
         *
         * @return boolean
         */
        public static function checkMail($email)
        {
            $email = filter_var((string) $email, FILTER_SANITIZE_EMAIL);
            $regex = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';
            
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match($regex, $email)) {
                return true;
            }
            
            return false;
        }
        
        /**
         * Verifica se o CPF é válido.
         *
         * @param string $cpf
         *
         * @return bool
         */
        public static function checkCpf($cpf)
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
         * Verifica se o CNPJ é válido.
         *
         * @param string $cnpj
         *
         * @return bool
         */
        public static function checkCnpj($cnpj)
        {
            $cnpj = (string) $cnpj;
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
         * Verifica se o TITULO do ELEITOR é válido.
         *
         * @param string $titleVoter
         *
         * @return bool
         */
        public static function checkTitleVoter($titleVoter)
        {
            $titleVoter = str_pad(preg_replace('[^0-9]', '', $titleVoter), 12, '0', STR_PAD_LEFT);
            $uf = intval(substr($titleVoter, 8, 2));
            
            if (strlen($titleVoter) != 12 || $uf < 1 || $uf > 28) {
                return false;
            } else {
                $d = 0;
                
                for ($i = 0; $i < 8; $i++) {
                    $d += $titleVoter{$i} * (9 - $i);
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
                
                if ($titleVoter{10} != $d) {
                    return false;
                }
                
                $d *= 2;
                
                for ($i = 8; $i < 10; $i++) {
                    $d += $titleVoter{$i} * (12 - $i);
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
                
                if ($titleVoter{11} != $d) {
                    return false;
                }
                
                return true;
            }
        }
        
        /**
         * Verifica se o dispositivo acessando o sistema é um celular/tablet.
         *
         * @return bool
         */
        public static function checkMobile()
        {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            
            // Detecta se está em CELULAR
            if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
                return true;
            } else {
                return false;
            }
        }
        
        /**
         * Verifica se o XML é válido
         *
         * @param \SimpleXMLElement|string $xml
         *
         * @return bool|\SimpleXMLElement|string
         */
        public static function checkXml($xml)
        {
            $xml = trim($xml);
            
            if (empty($xml)) {
                return false;
            }
            
            // Verifica se o documento é em HTML
            if (stripos($xml, '<!DOCTYPE html>') !== false) {
                return false;
            }
            
            // Lib XML
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xml);
            $errors = libxml_get_errors();
            libxml_clear_errors();
            
            if (!empty($errors)) {
                return false;
            }
            
            return $xml;
        }
        
        /**
         * @param string $json
         *
         * @return bool|array|string
         */
        public static function checkJson($json)
        {
            $assoc = version_compare(config('app.version.framework'), 'v2.1.0', '<=');
            $json = json_decode($json, $assoc);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }
            
            return $json;
        }
        
        /**
         * Recupera o IP Address do acesso ao sistema
         *
         * @return string
         */
        public static function getIpAddress()
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
         * Recupera as informações do dispositivo que está acessando o sistema.
         *
         * @return array
         */
        public static function getUserAgent()
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
         * Gera os fields da requisição simulando o http_build_query()
         *
         * @param array $array
         * @param string $prefix
         *
         * @return string|array
         */
        public static function buildQuery(array $array, $prefix = null)
        {
            if (!is_array($array)) {
                return $array;
            }
            
            $params = [];
            
            foreach ($array as $key => $value) {
                if (is_null($value)) {
                    continue;
                }
                
                if ($prefix && $key && !is_int($key)) {
                    $key = "{$prefix}[{$key}]";
                } else if ($prefix) {
                    $key = "{$prefix}[]";
                }
                
                if (is_array($value)) {
                    $params[] = self::buildQuery($value, $key);
                } else {
                    $params[] = $key.'='.urlencode($value);
                }
            }
            
            return implode('&', $params);
        }
        
        /**
         * Converte os bytes para um formato melhorado
         *
         * @param int $bytes
         * @param int $precision
         *
         * @return string
         */
        public static function convertBytes($bytes, $precision = 2)
        {
            $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            $bytes = max($bytes, 0);
            $base = floor(log($bytes) / log(1024));
            $base = min($base, count($units) - 1);
            $bytes = $bytes / pow(1000, $base);
            
            return number_format(
                    round($bytes, $precision), 2, ',', ''
                ).' '.$units[$base];
        }
        
        /**
         * @param object $object
         * @param string|array $methods
         *
         * @return bool
         */
        public static function checkMethods($object, $methods)
        {
            if (!is_array($methods)) {
                $methods = [$methods];
            }
            
            foreach ($methods as $method) {
                if (!empty($method) && method_exists($object, $method)) {
                    return true;
                }
            }
            
            return false;
        }
    }
}
