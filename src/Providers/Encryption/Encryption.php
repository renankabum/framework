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

namespace Core\Providers\Encryption {
    
    use RuntimeException;
    
    /**
     * Class Encrypter
     *
     * @package Core\Providers\Encryption
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Encryption
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
         * @param  string $key
         * @param  string $cipher
         */
        public function __construct($key, $cipher = 'AES-128-CBC')
        {
            $key = (string)$key;
            
            if (static::supported($key, $cipher)) {
                $this->key = $key;
                $this->cipher = $cipher;
            } else {
                throw new RuntimeException('As únicas cifras suportadas são AES-128-CBC e AES-256-CBC com os comprimentos de chave corretos.');
            }
        }
        
        /**
         * Determine if the given key and cipher combination is valid.
         *
         * @param  string $key
         * @param  string $cipher
         *
         * @return bool
         */
        public static function supported($key, $cipher)
        {
            $length = mb_strlen($key, '8bit');
            
            return ($cipher === 'AES-128-CBC' && $length === 16) ||
                   ($cipher === 'AES-256-CBC' && $length === 32);
        }
        
        /**
         * Create a new encryption key for the given cipher.
         *
         * @param  string $cipher
         *
         * @return string
         * @throws \Exception
         */
        public static function generateKey($cipher)
        {
            return random_bytes($cipher == 'AES-128-CBC' ? 16 : 32);
        }
        
        /**
         * Encrypt the given value.
         *
         * @param  mixed $value
         * @param  bool  $serialize
         *
         * @return string
         *
         * @throws RuntimeException
         * @throws \Exception
         */
        public function encrypt($value, $serialize = true)
        {
            $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
            $value = \openssl_encrypt($serialize ? serialize($value) : $value, $this->cipher, $this->key, 0, $iv);
            
            if ($value === false) {
                throw new RuntimeException('Não foi possível criptografar os dados.');
            }
            
            $mac = $this->hash($iv = base64_encode($iv), $value);
            $json = json_encode(compact('iv', 'value', 'mac'));
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Não foi possível criptografar os dados.');
            }
            
            return base64_encode($json);
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
         * Decrypt the given value.
         *
         * @param  mixed $payload
         * @param  bool  $unserialize
         *
         * @return string|bool
         *
         * @throws \RuntimeException
         * @throws \Exception
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
         * Get the JSON array from the given payload.
         *
         * @param  string $payload
         *
         * @return array|bool
         * @throws \Exception
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
         * @throws \Exception
         */
        protected function validMac(array $payload)
        {
            $calculated = $this->calculateMac($payload, $bytes = random_bytes(16));
            
            return hash_equals(
                hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated
            );
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
            return hash_hmac(
                'sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true
            );
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
