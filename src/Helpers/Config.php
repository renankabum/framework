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
     * Class Config
     *
     * @package Core\Helpers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Config
    {
        /**
         * @var array
         */
        protected static $config = [];
        
        /**
         * @param string $name
         * @param mixed  $default
         *
         * @return \Core\Helpers\Config|string
         */
        public static function load($name = null, $default = null)
        {
            self::$config = config($name, $default);
            
            if (!is_array(self::$config)) {
                return self::$config;
            }
            
            return new self();
        }
        
        /**
         * @return object
         */
        public function toObject()
        {
            return object_set(self::$config);
        }
        
        /**
         * @return array
         */
        public function toArray()
        {
            return self::$config;
        }
        
        /**
         * @return string
         */
        public function toJson()
        {
            return json_encode(self::$config, JSON_PRETTY_PRINT);
        }
    }
}
