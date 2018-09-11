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

namespace Core\Providers\Hash {
    
    use Core\Contracts\Hasher;
    
    /**
     * Class Argon
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Argon extends Hasher
    {
        /**
         * @var int
         */
        protected $memory = 1024;
        
        /**
         * @var int
         */
        protected $time = 2;
        
        /**
         * @var int
         */
        protected $threads = 2;
        
        /**
         * Cria um novo password hash usando um algoritmo forte de hash de via única
         *
         * @param string $value
         * @param array  $options
         *
         * @return bool|string
         */
        public function make($value, array $options = [])
        {
            $hashedValue = password_hash($value, PASSWORD_ARGON2I, [
                'memory_cost' => $this->memory($options),
                'time_cost' => $this->time($options),
                'threads' => $this->threads($options),
            ]);
            
            if ($hashedValue === false) {
                return false;
            }
            
            return $hashedValue;
        }
        
        /**
         * @param array $options
         *
         * @return int
         */
        protected function memory(array $options)
        {
            return isset($options['memory']) ? $options['memory'] : $this->memory;
        }
        
        /**
         * @param array $options
         *
         * @return int
         */
        protected function time(array $options)
        {
            return isset($options['time']) ? $options['time'] : $this->time;
        }
        
        /**
         * @param array $options
         *
         * @return int
         */
        protected function threads(array $options)
        {
            return isset($options['threads']) ? $options['threads'] : $this->threads;
        }
        
        /**
         * Esta função verifica se o hash fornecido implementa o algoritmo e as opções indicadas.
         * Se não, ela assume que o hash precisa ser regenerado.
         *
         * @param string $hashedValue
         * @param array  $options
         *
         * @return boolean
         */
        public function needsRehash($hashedValue, array $options = [])
        {
            return password_needs_rehash($hashedValue, PASSWORD_ARGON2I, [
                'memory_cost' => $this->memory($options),
                'time_cost' => $this->time($options),
                'threads' => $this->threads($options),
            ]);
        }
        
        /**
         * @param int $threads
         *
         * @return Argon
         */
        public function setThreads($threads)
        {
            $this->threads = $threads;
            
            return $this;
        }
        
        /**
         * @param int $memory
         *
         * @return Argon
         */
        public function setMemory($memory)
        {
            $this->memory = $memory;
            
            return $this;
        }
        
        /**
         * @param int $time
         *
         * @return Argon
         */
        public function setTime($time)
        {
            $this->time = $time;
            
            return $this;
        }
    }
}
