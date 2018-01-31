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

namespace Core\Providers\Hash {
    
    /**
     * Class BcryptHasher
     *
     * @package Core\Providers\Hash
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class BcryptHasher
    {
        /**
         * @var int
         */
        protected $rounds = 10;
        
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
            $hash = password_hash($password, PASSWORD_BCRYPT, [
                'cost' => $this->cost($options),
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
        
        /**
         * Extraia o valor de custo da matriz de opções.
         *
         * @param array $options
         *
         * @return int
         */
        protected function cost(array $options)
        {
            return isset($options['rounds']) ? $options['rounds'] : $this->rounds;
        }
    }
}
