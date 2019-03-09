<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 09/03/2019 Vagner Cardoso
 */

namespace Core\Providers\View {
    
    use Core\Contracts\Provider;
    
    /**
     * Class ViewProvider.
     *
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class ViewProvider extends Provider
    {
        /**
         * Registra o serviço de template.
         */
        public function register()
        {
            $this->container['view'] = function () {
                return new Twig(
                    config('view.template.view'), config('view.options')
                );
            };
            
            $this->container['view-mail'] = function () {
                return new Twig(
                    config('view.template.mail'), config('view.options')
                );
            };
        }
        
        /**
         * Registra outros serviços no escopo do provider.
         */
        public function boot()
        {
            // Ativa debug
            $this->view->addExtension(new \Twig_Extension_Debug());
            
            // Registra as funções e filtros
            foreach (config('view.registers') as $key => $items) {
                foreach ($items as $name => $item) {
                    switch ($key) {
                        case 'functions':
                            $this->view->addFunction($name, $item);
                            break;
                        
                        case 'filters':
                            $this->view->addFilter($name, $item);
                            break;
                    }
                }
            }
        }
    }
}
