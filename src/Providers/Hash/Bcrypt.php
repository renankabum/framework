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

namespace Core\Providers\Hash {
    
    use Core\Contracts\Hasher;
    
    /**
     * Class Bcrypt
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Bcrypt extends Hasher
    {
        /**
         * @var int
         */
        protected $rounds = 10;
        
        /**
         * Cria um novo password hash usando um algoritmo forte de hash de via única
         *
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
                return false;
            }
            
            return $hashedValue;
        }
        
        /**
         * Extraia o valor de custo da matriz de opções.
         *
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
        
        /**
         * Esta função verifica se o hash fornecido implementa o algoritmo e as opções indicadas.
         * Se não, ela assume que o hash precisa ser regenerado.
         *
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
         * Defina o fator de trabalho de senha padrão.
         *
         * @param int $rounds
         *
         * @return $this
         */
        public function setRounds($rounds)
        {
            $this->rounds = (int) $rounds;
            
            return $this;
        }
    }
}
