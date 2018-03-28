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
    
    /**
     * Class ArgonHasher
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class ArgonHasher
    {
        /**
         * @var int
         */
        protected $threads = 2;
        
        /**
         * @var int
         */
        protected $memory = 1024;
        
        /**
         * @var int
         */
        protected $time = 2;
        
        /**
         * Retorna a informações sobre o hash fornecido
         *
         * @param string $hash
         *
         * @return array
         */
        public function info($hash)
        {
            return password_get_info($hash);
        }
        
        /**
         * Cria um novo password hash usando um algoritmo forte de hash de via única
         *
         * @param string $password
         * @param array  $options
         *
         * @return bool|string
         */
        public function make($password, array $options = [])
        {
            $hash = password_hash($password, PASSWORD_ARGON2I, [
                'memory_cost' => $this->memory($options),
                'time_cost' => $this->time($options),
                'threads' => $this->threads($options),
            ]);
            
            if ($hash === false) {
                return false;
            }
            
            return $hash;
        }
        
        /**
         * Verifica se o hash fornecido corresponde com o password fornecido.
         *
         * @param string $password
         * @param string $hash
         *
         * @return boolean
         */
        public function check($password, $hash)
        {
            if (strlen($hash) === 0) {
                return false;
            }
            
            return password_verify($password, $hash);
        }
        
        /**
         * Esta função verifica se o hash fornecido implementa o algoritmo e as opções indicadas.
         * Se não, ela assume que o hash precisa ser regenerado.
         *
         * @param string $hash
         * @param array  $options
         *
         * @return boolean
         */
        public function needsRehash($hash, array $options = [])
        {
            return password_needs_rehash($hash, PASSWORD_BCRYPT, [
                'memory_cost' => $this->memory($options),
                'time_cost' => $this->time($options),
                'threads' => $this->threads($options),
            ]);
        }
        
        /**
         * @param int $threads
         *
         * @return ArgonHasher
         */
        public function setThreads($threads)
        {
            $this->threads = $threads;
            
            return $this;
        }
        
        /**
         * @param int $memory
         *
         * @return ArgonHasher
         */
        public function setMemory($memory)
        {
            $this->memory = $memory;
            
            return $this;
        }
        
        /**
         * @param int $time
         *
         * @return ArgonHasher
         */
        public function setTime($time)
        {
            $this->time = $time;
            
            return $this;
        }
        
        /**
         * @param array $options
         *
         * @return int
         */
        protected function memory(array $options)
        {
            return isset($options['memory'])
                ? $options['memory']
                : $this->memory;
        }
        
        /**
         * @param array $options
         *
         * @return int
         */
        protected function time(array $options)
        {
            return isset($options['time'])
                ? $options['time']
                : $this->time;
        }
        
        /**
         * @param array $options
         *
         * @return int
         */
        protected function threads(array $options)
        {
            return isset($options['threads'])
                ? $options['threads']
                : $this->threads;
        }
    }
}
