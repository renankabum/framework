<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Providers\Session {
    
    /**
     * Class Flash
     *
     * @package App\Core\Providers\Session
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Flash
    {
        /**
         * Flash key
         *
         * @var string
         */
        protected $key = '__flash__';
        
        /**
         * Flash data
         *
         * @var array
         */
        protected $data = [];
        
        /**
         * Flash storage
         *
         * @var null|array
         */
        protected $storage;
        
        /**
         * Flash constructor.
         */
        public function __construct()
        {
            if (!isset($_SESSION)) {
                throw new \RuntimeException('[FLASH] :: Session not started.');
            }
            
            $this->storage = &$_SESSION[$this->key];
            
            if (!empty($this->storage) && is_array($this->storage)) {
                $this->data = $this->storage;
            }
            
            $this->storage = [];
        }
        
        /**
         * Adiciona uma nova mensagem
         *
         * @param $name
         * @param $value
         */
        public function add($name, $value)
        {
            // Cria um array vasio caso não exista a key
            if (empty($this->storage[$name])) {
                $this->storage[$name] = [];
            }
            
            // Adiciona uma nova mensagem
            $this->storage[$name] = $value;
        }
        
        /**
         * Retorna todas mensagems
         *
         * @return array
         */
        public function all()
        {
            return $this->get();
        }
        
        /**
         * Recupera a mensagem
         *
         * @param string $key
         *
         * @return mixed
         */
        public function get($key = null)
        {
            $data = $this->data;
            
            if (empty($key)) {
                return $data;
            }
            
            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
            
            foreach (explode('.', $key) as $segment) {
                if (is_array($data) && array_key_exists($segment, $data)) {
                    $data = $data[$segment];
                } else {
                    return null;
                }
            }
            
            return $data;
        }
        
        /**
         * Verifica a mensagem
         *
         * @param string $key
         *
         * @return mixed
         */
        public function has($key)
        {
            $data = $this->data;
            
            if (is_null($key)) {
                return false;
            }
            
            $key = (array) $key;
            
            if (!$data) {
                return false;
            }
            
            if ($data === []) {
                return false;
            }
            
            foreach ($key as $item) {
                if (array_key_exists($item, $data)) {
                    continue;
                }
                
                foreach (explode('.', $item) as $segment) {
                    if (is_array($data) && array_key_exists($data, $segment)) {
                        $data = $data[$segment];
                    } else {
                        return false;
                    }
                }
            }
            
            return $data;
        }
    }
}
