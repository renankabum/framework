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
         * @param string $password
         * @param array  $options
         *
         * @return bool|string
         */
        public function make($password, array $options = [])
        {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->cost($options)]);

            if ($hash === false) {
                return false;
            }

            return $hash;
        }

        /**
         * @param string $password
         * @param string $hash
         *
         * @return bool
         */
        public function check($password, $hash)
        {
            if (strlen($hash) === 0) {
                return false;
            }

            return password_verify($password, $hash);
        }

        /**
         * @param string $hash
         * @param array  $options
         *
         * @return bool
         */
        public function needsRehash($hash, array $options = [])
        {
            return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $this->cost($options),]);
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
         * @return int|mixed
         */
        protected function cost(array $options)
        {
            return isset($options['rounds']) ? $options['rounds'] : $this->rounds;
        }
    }
}
