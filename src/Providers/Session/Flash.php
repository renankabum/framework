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
    
    use Core\App;
    use Core\Helpers\Arr;
    
    /**
     * Class Flash
     *
     * @package App\Core\Providers\Session
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Flash
    {
        /**
         * @var string
         */
        protected $key = '_flash';
        
        /***
         * @var array
         */
        protected $data = [];
        
        /**
         * @var array
         */
        protected $storage;
        
        /**
         * Flash constructor.
         */
        public function __construct()
        {
            if (!isset($_SESSION)) {
                App::getInstance()->resolve('session')->start();
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
         * @param string $name
         * @param mixed  $value
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
            return $this->data;
        }
        
        /**
         * Recupera a mensagem
         *
         * @param string $key
         * @param string $default
         *
         * @return mixed
         */
        public function get($key, $default = null)
        {
            return Arr::get($this->data, $key, $default);
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
            return Arr::has($this->data, $key);
        }
    }
}
