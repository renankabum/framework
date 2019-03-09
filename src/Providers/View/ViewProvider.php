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

namespace Core\Providers\View {
    
    use Core\App;
    use Core\Contracts\Provider as BaseProvider;
    
    /**
     * Class ViewProvider
     *
     * @package Core\Providers\View
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class ViewProvider extends BaseProvider
    {
        /**
         * Registra o serviço de template
         *
         * @return void
         */
        public function register()
        {
            $this->container['view'] = function () {
                return new Twig(
                    config('view.templates'), config('view.options')
                );
            };
        }
        
        /**
         * Registra outros serviços no escopo do provider
         *
         * @return void
         */
        public function boot()
        {
            // Ativa debug
            $this->view->addExtension(new \Twig_Extension_Debug());
            
            // View providers
            $this->defaults();
            
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
        
        /**
         * Funções e filtros padrão
         */
        protected function defaults()
        {
            // Verifica se existe o container
            $this->view->addFunction('has_container', [App::getInstance(), 'resolve']);
        }
    }
}
