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
            // Verifica se a sessão está ativa
            if (config('app.session')) {
                // Sessão
                $this->container['session'] = function () {
                    return new Session();
                };
                
                // Flash Message
                $this->container['flash'] = function () {
                    return new Flash();
                };
            }
        }
        
        /**
         * Registra outros serviços no escopo do provider
         *
         * @return void
         */
        public function boot()
        {
            // Verifica se a sessão está ativa
            if (config('app.session')) {
                $this->view->addGlobal('session', $this->session->all());
                $this->view->addGlobal('flash', $this->flash->all());
            }
        }
    }
}
