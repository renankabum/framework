<?php

/**
 * NAVEGARTE Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Providers\Encryption;

use RuntimeException;
use Slim\Container;

/**
 * Class Encryption
 *
 * @package Navegarte\Providers\Encryption
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Encryption
{
    /**
     * @var string
     */
    protected $key;
    
    /**
     * @var string
     */
    protected $cipher;
    
    /**
     * @var \Slim\Container
     */
    protected $container;
    
    /**
     * Encryption constructor.
     *
     * @param \Slim\Container $container
     * @param string          $key
     * @param string          $cipher
     */
    public function __construct(Container $container, $key, $cipher = 'AES-128-CBC')
    {
        $this->container = $container;
        
        $key = (string)$key;
        
        if (static::supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new RuntimeException('As únicas cifras suportadas são AES-128-CBC e AES-256-CBC com os comprimentos de chave corretos.');
        }
    }
    
    /**
     * @param string $key
     * @param string $cipher
     *
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        $length = mb_strlen($key, '8bit');
        
        return ($cipher === 'AES-128-CBC' && $length === 16) || ($cipher === 'AES-256-CBC' && $length === 32);
    }
    
    /**
     * @param mixed $value
     * @param bool  $serialize
     *
     * @return string
     */
    public function encrypt($value, $serialize = true)
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        
        $valueSerialize = ($serialize ? serialize($value) : $value);
        $value = openssl_encrypt($valueSerialize, $this->cipher, $this->key, 0, $iv);
        
        if ($value === false) {
            throw new RuntimeException('Não foi possível criptografar os dados.');
        }
        
        $mac = $this->hash($iv = base64_encode($iv), $value);
        
        $json = json_encode(compact('iv', 'value', 'mac'));
        
        if (!is_string($json)) {
            throw new RuntimeException('Não foi possível criptografar os dados.');
        }
        
        return base64_encode($json);
    }
    
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }
    
    /**
     * @param mixed $payload
     * @param bool  $unserialize
     *
     * @return mixed|string
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);
        
        $iv = base64_decode($payload['iv']);
        
        $decrypt = openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);
        
        return $unserialize ? unserialize($decrypt) : $decrypt;
    }
    
    /**
     * @param mixed $payload
     *
     * @return string
     */
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }
    
    /**
     * @param string $payload
     *
     * @return array|bool
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);
        
        if (!is_array($payload) && !isset($payload['iv'], $payload['value'], $payload['mac'])) {
            //throw new RuntimeException('O Payload não é válido.');
            return false;
        }
        
        $calculateMac = hash_hmac('sha256', $this->hash($payload['iv'], $payload['value']), $bytes = random_bytes(16), true);
        
        if (!hash_equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calculateMac)) {
            //throw new RuntimeException('O Hash Mac não é válido.');
            return false;
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
    
    // METHODS PROTECTS
    
    /**
     * @param string $iv
     * @param string $value
     *
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv . $value, $this->key);
    }
    
    // IMPLEMENTAR DEPOIS
    protected function validPayload($payload)
    {
        // TODO: Implement validPayload() method.
    }
    
    protected function validMac(array $payload)
    {
        // TODO: Implement validMac() method.
    }
    
    protected function calculateMac($payload, $bytes)
    {
        // TODO: Implement calculateMac() method.
    }
}
