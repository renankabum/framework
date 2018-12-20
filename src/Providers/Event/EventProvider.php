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

namespace Core\Providers\Event {
    
    use Core\Contracts\Provider;
    
    /**
     * Class EventProvider
     *
     * @package Core\Providers\Event
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class EventProvider extends Provider
    {
        /**
         * Registra serviço para emissão de eventos
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \Core\Providers\Event\Event
             */
            $this->container['event'] = function () {
                return new Event();
            };
        }
        
        /**
         * Registra funções para a view para emitir os eventos
         *
         * @return void
         */
        public function boot()
        {
            $this->view->addFunction('event_emit', function ($event) {
                $this->event->emit((string) $event);
            });
        }
    }
}
