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

namespace Core\Providers\Session {
    
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
         * Atualiza o id da sessão atual com um novo id gerado
         */
        public function regenerate()
        {
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
        }
        
        /**
         * Destrói todos os dados registrados em uma sessão
         */
        public function destroy()
        {
            $_SESSION = [];
            
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                
                setcookie(
                    session_name(),
                    '',
                    (time() - 42000),
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_destroy();
            }
        }
    }
}
