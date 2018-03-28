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

namespace Core\Providers\Session {
    
    /**
     * Class Flash
     *
     * @package App\Core\Providers\Session
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Flash
    {
        /**
         * Flash key
         *
         * @var string
         */
        protected $key = '__vcFlash';
        
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
                throw new \RuntimeException('Session not started.');
            }
            
            $this->storage = &$_SESSION;
            
            if (!empty($this->storage[$this->key]) && is_array($this->storage[$this->key])) {
                $this->data = $this->storage[$this->key];
            }
            
            $this->storage[$this->key] = [];
        }
        
        /**
         * Adiciona uma nova mensagem
         *
         * @param $key
         * @param $message
         */
        public function add($key, $message)
        {
            // Cria um array vasio caso não exista a key
            if (empty($this->storage[$this->key][$key])) {
                $this->storage[$this->key][$key] = [];
            }
            
            // Adiciona uma nova mensagem
            $this->storage[$this->key][$key] = $message;
        }
        
        /**
         * Seta um novo alerta
         *
         * @param $type
         * @param $message
         */
        public function set($type, $message)
        {
            $this->add('alert', ['type' => $type, 'message' => $message]);
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
            $messages = $this->data;
            
            if (is_null($key)) {
                return $messages;
            }
            
            if (array_key_exists($key, $messages)) {
                return $messages[$key];
            }
            
            foreach (explode('.', $key) as $segment) {
                if (is_array($messages) && array_key_exists($segment, $messages)) {
                    $messages = $messages[$segment];
                } else {
                    return null;
                }
            }
            
            return $messages;
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
            $messages = $this->data;
            
            if (is_null($key)) {
                return false;
            }
            
            $key = (array)$key;
            
            if (!$messages) {
                return false;
            }
            
            if ($messages === []) {
                return false;
            }
            
            foreach ($key as $item) {
                if (array_key_exists($item, $messages)) {
                    continue;
                }
                
                foreach (explode('.', $item) as $segment) {
                    if (is_array($messages) && array_key_exists($messages, $segment)) {
                        $messages = $messages[$segment];
                    } else {
                        return false;
                    }
                }
            }
            
            return $messages;
        }
    }
}
