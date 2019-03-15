<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 28/04/2017 Vagner Cardoso
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
                return Event::getInstance();
            };
        }
        
        /**
         * Registra funções para a view para emitir os eventos
         *
         * @return void
         */
        public function boot()
        {
            /**
             * Dispara um evento
             */
            $this->view->addFunction('event_emit', function ($event) {
                $arguments = func_get_args();
                array_shift($arguments);
                
                return $this->event->emit(
                    (string) $event, ...$arguments
                );
            });
            
            /**
             * Verifica se o evento existe
             */
            $this->view->addFunction('event_has', function ($event) {
                if (empty($event)) {
                    return false;
                }
                
                return $this->event->events(
                    $event
                );
            });
        }
    }
}
