<?php

/**
 * Core Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - Core
 */

namespace Core\Providers\Encryption;

use RuntimeException;
use Slim\Container;

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
     * @var \Slim\Container
     */
    protected $container;
    
    /**
     * Create a new encrypter instance.
     *
     * @param \Slim\Container $container
     * @param  string         $key
     * @param  string         $cipher
     */
    public function __construct(Container $container, $key, $cipher = 'AES-128-CBC')
    {
        $this->container = $container;
        
        $key = (string)$key;
        
        if (static::supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
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
        
        return ($cipher === 'AES-128-CBC' && $length === 16) || ($cipher === 'AES-256-CBC' && $length === 32);
    }
    
    /**
     * Create a new encryption key for the given cipher.
     *
     * @param  string $cipher
     *
     * @return string
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
     */
    public function encrypt($value, $serialize = true)
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
    
        // First we will encrypt the value using OpenSSL. After this is encrypted we
        // will proceed to calculating a MAC for the encrypted value so that this
        // value can be verified later as not having been changed by the users.
        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value, $this->cipher, $this->key, 0, $iv
        );
        
        if ($value === false) {
            throw new RuntimeException('Could not encrypt the data.');
        }
    
        // Once we have the encrypted value we will go ahead base64_encode the input
        // vector and create the MAC for the encrypted value so we can verify its
        // authenticity. Then, we'll JSON encode the data in a "payload" array.
        $mac = $this->hash($iv = base64_encode($iv), $value);
        
        $json = json_encode(compact('iv', 'value', 'mac'));
        
        if (!is_string($json)) {
            throw new RuntimeException('Could not encrypt the data.');
        }
        
        return base64_encode($json);
    }
    
    /**
     * Encrypt a string without serialization.
     *
     * @param  string $value
     *
     * @return string
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
     * @return string
     *
     * @throws \RuntimeException
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);
        
        $iv = base64_decode($payload['iv']);
    
        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        $decrypted = \openssl_decrypt(
            $payload['value'], $this->cipher, $this->key, 0, $iv
        );
    
        if ($decrypted === false) {
            throw new RuntimeException('Could not decrypt the data.');
        }
    
        return $unserialize ? unserialize($decrypted) : $decrypted;
    }
    
    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string $payload
     *
     * @return string
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
        return hash_hmac('sha256', $iv . $value, $this->key);
    }
    
    /**
     * Get the JSON array from the given payload.
     *
     * @param  string $payload
     *
     * @return array
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);
        
        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (!$this->validPayload($payload)) {
            throw new RuntimeException('The payload is invalid.');
        }
        
        if (!$this->validMac($payload)) {
            throw new RuntimeException('The MAC is invalid.');
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
        return is_array($payload) && isset(
                $payload['iv'], $payload['value'], $payload['mac']
            );
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
