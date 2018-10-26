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
    
    use Core\Contracts\Provider as BaseProvider;
    
    /**
     * Class SessionProvider
     *
     * @package Core\Providers\Session
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class SessionProvider extends BaseProvider
    {
        /**
         * Registra os serviços da sessões
         *
         * @return void
         */
        public function register()
        {
            // Sessão
            $this->container['session'] = function () {
                if (!is_php_cli() && config('app.session')) {
                    return new Session();
                }
                
                return false;
            };
            
            // Flash Message
            $this->container['flash'] = function () {
                if (!is_php_cli() && config('app.session')) {
                    return new Flash();
                }
                
                return false;
            };
        }
        
        /**
         * Registra outros serviços no escopo do provider
         *
         * @return void
         */
        public function boot()
        {
            if (!is_php_cli() && config('app.session')) {
                $this->view->addGlobal('session', $this->session->all());
                $this->view->addGlobal('flash', $this->flash->all());
            }
        }
    }
}
