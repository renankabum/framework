<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Providers\Jwt {
    
    use Core\Helpers\Base64;
    
    /**
     * Class Jwt
     *
     * @package Core\Providers\Jwt
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Jwt
    {
        /**
         * Security key
         *
         * @var string
         */
        protected $key;
        
        /**
         * Supported Algorithms
         *
         * @var array
         */
        protected $algorithms = [
            'HS256' => ['hash_hmac', 'SHA256'],
            'HS512' => ['hash_hmac', 'SHA512'],
            'HS384' => ['hash_hmac', 'SHA384'],
        ];
        
        /**
         * Jwt constructor.
         *
         * @param string $key
         */
        public function __construct($key)
        {
            $this->key = (string) $key;
            
            if (empty($this->key)) {
                throw new \InvalidArgumentException("[JWT] The security guard can not be watered.", E_USER_ERROR);
            }
        }
        
        /**
         * Converts and signs a string in jwt.
         *
         * @param array  $payload
         * @param string $algorithm
         * @param array  $header
         *
         * @return string
         */
        public function encode(array $payload, $algorithm = 'HS256', array $header = [])
        {
            $array = [];
            
            // Headers
            $header = array_merge($header, [
                'typ' => 'Jwt',
                'alg' => $algorithm,
            ]);
            
            $array[] = Base64::encode(json_encode($header));
            
            // Payload
            $array[] = Base64::encode(json_encode($payload));
            
            // Signature
            $signature = $this->signature(implode('.', $array), $algorithm);
            $array[] = Base64::encode($signature);
            
            return implode('.', $array);
        }
        
        /**
         * Decode the jwt string.
         *
         * @param string $token
         *
         * @return array
         * @throws \Exception
         */
        public function decode($token)
        {
            $split = explode('.', $token);
            
            if (count($split) != 3) {
                throw new \InvalidArgumentException("[JWT] The token does not contain a valid format.", E_USER_ERROR);
            }
            
            // Separate the token
            list($header64, $payload64, $signature) = $split;
            
            if (!$header = json_decode(Base64::decode($header64), true, 512, JSON_BIGINT_AS_STRING)) {
                throw new \UnexpectedValueException("[JWT] Invalid header encoding.", E_USER_ERROR);
            }
            
            if (!$payload = json_decode(Base64::decode($payload64), true, 512, JSON_BIGINT_AS_STRING)) {
                throw new \UnexpectedValueException("[JWT] Invalid payload encoding.", E_USER_ERROR);
            }
            
            if (!$signature = Base64::decode($signature)) {
                throw new \UnexpectedValueException("[JWT] Invalid signature encoding.", E_USER_ERROR);
            }
            
            if (empty($header['alg'])) {
                throw new \UnexpectedValueException("[JWT] Empty algorithm.", E_USER_ERROR);
            }
            
            if (!array_key_exists($header['alg'], $this->algorithms)) {
                throw new \UnexpectedValueException("[JWT] Algorithm {$header['alg']} is not supported.", E_USER_ERROR);
            }
            
            if (!$this->validate("{$header64}.{$payload64}", $signature, $header['alg'])) {
                throw new \Exception("[JWT] Signature verification failed.", E_USER_ERROR);
            }
            
            return $payload;
        }
        
        /**
         * @return string
         */
        public function getKey()
        {
            return $this->key;
        }
        
        /**
         * @param string $key
         *
         * @return Jwt
         */
        public function setKey($key)
        {
            $this->key = $key;
            
            return $this;
        }
        
        /**
         * Generate Token Signature
         *
         * @param string $hashed
         * @param string $algorithm
         *
         * @return string
         */
        private function signature($hashed, $algorithm = 'HS256')
        {
            if (!array_key_exists($algorithm, $this->algorithms)) {
                throw new \InvalidArgumentException("[JWT] Algorithm {$algorithm} is not supported.", E_USER_ERROR);
            }
            
            // Separa o algoritimo
            list($function, $algorithm) = $this->algorithms[$algorithm];
            
            switch ($function) {
                case 'hash_hmac':
                    return hash_hmac($algorithm, $hashed, $this->key, true);
                    break;
            }
        }
        
        /**
         * Token signature validation
         *
         * @param string $hashed
         * @param string $signature
         * @param string $algorithm
         *
         * @return bool
         */
        private function validate($hashed, $signature, $algorithm = 'HS256')
        {
            if (!array_key_exists($algorithm, $this->algorithms)) {
                throw new \InvalidArgumentException("[JWT] Algorithm {$algorithm} is not supported.", E_USER_ERROR);
            }
            
            // Separa o algoritimo
            list($function, $algorithm) = $this->algorithms[$algorithm];
            
            switch ($function) {
                case 'hash_hmac':
                    $hashed = hash_hmac($algorithm, $hashed, $this->key, true);
                    
                    if (function_exists('hash_equals')) {
                        return hash_equals($signature, $hashed);
                    }
                    
                    return $signature === $hashed;
                    break;
            }
            
            return false;
        }
    }
}
