<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Helpers;

/**
 * Class Config
 *
 * @package Core\Helpers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Config implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $items;
    
    /**
     * Config constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
    
    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);
    }
    
    /**
     * @param array|string $key
     * @param mixed        $default
     *
     * @return array|mixed
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }
        
        return Arr::get($this->items, $key, $default);
    }
    
    /**
     * @param array $keys
     *
     * @return array
     */
    public function getMany(array $keys)
    {
        $config = [];
        
        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                list($key, $default) = [$default, null];
            }
            
            $config[$key] = Arr::get($this->items, $key, $default);
        }
        
        return $config;
    }
    
    /**
     * @param array|string $key
     * @param mixed        $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key, $value];
        
        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }
    }
    
    /**
     * @param array|string $key
     * @param mixed        $value
     *
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);
        
        array_unshift($array, $value);
        
        $this->set($key, $array);
    }
    
    /**
     * @param array|string $key
     * @param mixed        $value
     *
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);
        
        $array[] = $value;
        
        $this->set($key, $array);
    }
    
    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
    
    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    
    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    
    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }
}
