<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-${YEAH} Vagner Cardoso - Core
 */

namespace Core\Providers\Session;

use Core\Helpers\Arr;

/**
 * Class Session
 *
 * @package Core\Providers\Session
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Session
{
    /**
     * @var array
     */
    protected $session = [];
    
    /**
     * Session constructor.
     */
    public function __construct()
    {
        $this->verifySessionExists();
    
        $this->session = &$_SESSION;
    }
    
    /**
     * @return array
     */
    public function all()
    {
        return $this->session;
    }
    
    /**
     * @param string $key
     * @param null   $value
     */
    public function set($key, $value = null)
    {
        if (!is_array($key)) {
            $key = [$key => $value];
        }
        
        foreach ($key as $arrayKey => $arrayValue) {
            Arr::set($this->session, $arrayKey, $arrayValue);
        }
    }
    
    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->session, $key, $default);
    }
    
    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return Arr::exists($this->session, $key);
    }
    
    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return !is_null($this->get($key));
    }
    
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function remove($key)
    {
        return Arr::pull($this->session, $key);
    }
    
    /**
     * @param string $keys
     */
    public function forget($keys)
    {
        Arr::forget($this->session, $keys);
    }
    
    /**
     * Remove todas sessão
     */
    public function destroy()
    {
        session_destroy();
        
        $_SESSION = [];
    }
    
    /**
     * Iniciar a session caso ela não existe
     */
    public function verifySessionExists()
    {
        if (!session_id()) {
            $current = session_get_cookie_params();
    
            session_set_cookie_params($current['lifetime'], $current['path'], $current['domain'], $current['secure'], true);
            session_name(md5(md5('Core' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['PHP_SELF'])));
            session_cache_limiter('nocache');
            
            session_start();
        }
    }
}
