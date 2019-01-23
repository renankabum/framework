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

namespace Core\Providers\Encryption {
    
    /**
     * Class Encrypter
     *
     * @package Core\Providers\Encryption
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Encryption
    {
        /**
         * The encryption key.
         *
         * @var string
         */
        protected $key;
        
        /**
         * The algorithm used for encryption.
         *
         * @var string
         */
        protected $cipher;
        
        /**
         * Create a new encrypter instance.
         *
         * @param string $key
         * @param string $cipher
         */
        public function __construct($key, $cipher = 'AES-256-CBC')
        {
            $this->key = $key;
            $this->cipher = $cipher;
            
            if (empty($this->key)) {
                throw new \InvalidArgumentException('[ENCRYPTION] Empty key.', E_ERROR);
            }
        }
        
        /**
         * Encrypt a string without serialization.
         *
         * @param  string $value
         *
         * @return string
         * @throws \Exception
         */
        public function encryptString($value)
        {
            return $this->encrypt($value, false);
        }
        
        /**
         * Encrypt the given value.
         *
         * @param  mixed $value
         * @param  bool  $serialize
         *
         * @return string
         */
        public function encrypt($value, $serialize = true)
        {
            $ivlenght = openssl_cipher_iv_length($this->cipher);
            
            // Verifica a versão do PHP
            if (PHP_MAJOR_VERSION > 5) {
                $iv = random_bytes($ivlenght);
            } else {
                $iv = openssl_random_pseudo_bytes($ivlenght);
            }
            
            $value = \openssl_encrypt($serialize ? serialize($value) : $value, $this->cipher, $this->key, 0, $iv);
            
            if ($value === false) {
                return false;
            }
            
            $mac = $this->hash($iv = base64_encode($iv), $value);
            $json = json_encode(compact('iv', 'value', 'mac'));
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }
            
            return base64_encode($json);
        }
        
        /**
         * Create a MAC for the given value.
         *
         * @param  string $iv
         * @param  mixed  $value
         *
         * @return string
         */
        protected function hash($iv, $value)
        {
            return hash_hmac('sha256', $iv.$value, $this->key);
        }
        
        /**
         * Decrypt the given string without unserialization.
         *
         * @param  string $payload
         *
         * @return string
         * @throws \Exception
         */
        public function decryptString($payload)
        {
            return $this->decrypt($payload, false);
        }
        
        /**
         * Decrypt the given value.
         *
         * @param  mixed $payload
         * @param  bool  $unserialize
         *
         * @return string|bool
         */
        public function decrypt($payload, $unserialize = true)
        {
            $payload = $this->getJsonPayload($payload);
            $iv = base64_decode($payload['iv']);
            $decrypted = \openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);
            
            if ($decrypted === false) {
                return false;
            }
            
            return $unserialize ? unserialize($decrypted) : $decrypted;
        }
        
        /**
         * Get the JSON array from the given payload.
         *
         * @param  string $payload
         *
         * @return array|bool
         */
        protected function getJsonPayload($payload)
        {
            $payload = json_decode(base64_decode($payload), true);
            
            if (!$this->validPayload($payload)) {
                return false;
            }
            
            if (!$this->validMac($payload)) {
                return false;
            }
            
            return $payload;
        }
        
        /**
         * Verify that the encryption payload is valid.
         *
         * @param  mixed $payload
         *
         * @return bool
         */
        protected function validPayload($payload)
        {
            return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
                strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
        }
        
        /**
         * Determine if the MAC for the given payload is valid.
         *
         * @param  array $payload
         *
         * @return bool
         */
        protected function validMac(array $payload)
        {
            $bytes = (PHP_MAJOR_VERSION > 5) ? random_bytes(16) : openssl_random_pseudo_bytes(16);
            
            $calculated = $this->calculateMac($payload, $bytes);
            
            return hash_equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated);
        }
        
        /**
         * Calculate the hash of the given payload.
         *
         * @param  array  $payload
         * @param  string $bytes
         *
         * @return string
         */
        protected function calculateMac($payload, $bytes)
        {
            return hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true);
        }
        
        /**
         * Get the encryption key.
         *
         * @return string
         */
        public function getKey()
        {
            return $this->key;
        }
    }
}
