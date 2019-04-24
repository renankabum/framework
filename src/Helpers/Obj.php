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
     * Class Obj
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Obj
    {
        /**
         * Converte um array em um objeto
         *
         * @param array $array
         *
         * @return bool|\stdClass
         */
        public static function fromArray(array $array)
        {
            $object = new \stdClass();
            
            if (empty($array)) {
                return $object;
            }
            
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $object->{$key} = self::fromArray($value);
                } else {
                    $object->{$key} = isset($value) ? $value : '';
                }
            }
            
            return $object;
        }
        
        /**
         * Adiciona elemento em um objeto
         *
         * @param array $array
         *
         * @return \stdClass
         */
        public static function set(array $array)
        {
            return self::fromArray($array);
        }
        
        /**
         * Recupera o valor do objeto
         *
         * @param object $object
         * @param string $name
         * @param mixed $default
         *
         * @return mixed
         */
        public static function get($object, $name = null, $default = null)
        {
            if (empty($name)) {
                return $object;
            }
            
            foreach (explode('.', $name) as $segment) {
                if (is_object($object) || isset($object->{$segment})) {
                    $object = $object->{$segment};
                } else {
                    return $default;
                }
            }
            
            return $object;
        }
        
        /**
         * Converte um objeto em um json
         *
         * @param object $object
         *
         * @return string
         */
        public static function toJson($object)
        {
            return json_encode(
                self::toArray($object)
            );
        }
        
        /**
         * Converte um objeto em um array
         *
         * @param object $object
         *
         * @return array
         */
        public static function toArray($object)
        {
            $array = [];
            
            foreach ($object as $key => $value) {
                if (!isset($value) && trim($value) == '') {
                    return $array;
                }
                
                if (is_object($value) || is_array($value)) {
                    $array[$key] = self::toArray($value);
                } else {
                    if (isset($key)) {
                        $array[$key] = $value;
                    }
                }
            }
            
            return $array;
        }
    }
}
