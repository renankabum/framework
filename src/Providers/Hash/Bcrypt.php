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

namespace Core\Providers\Hash {
    
    /**
     * Class Bcrypt
     *
     * @package Core\Providers\Hash
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Bcrypt extends Hash
    {
        /**
         * @var int
         */
        protected $rounds = 10;
        
        /**
         * @param string $value
         * @param array $options
         *
         * @return bool|string
         */
        public function make($value, array $options = [])
        {
            $hashedValue = password_hash($value, PASSWORD_BCRYPT, [
                'cost' => $this->cost($options),
            ]);
            
            if ($hashedValue === false) {
                throw new \RuntimeException(
                    "Bcrypt hashing not supported"
                );
            }
            
            return $hashedValue;
        }
        
        /**
         * @param string $hashedValue
         * @param array $options
         *
         * @return boolean
         */
        public function needsRehash($hashedValue, array $options = [])
        {
            return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, [
                'cost' => $this->cost($options),
            ]);
        }
        
        /**
         * @param int $rounds
         *
         * @return $this
         */
        public function setRounds($rounds)
        {
            $this->rounds = (int) $rounds;
            
            return $this;
        }
        
        /**
         * @param array $options
         *
         * @return int
         */
        protected function cost(array $options)
        {
            return isset($options['rounds'])
                ? $options['rounds']
                : $this->rounds;
        }
    }
}
