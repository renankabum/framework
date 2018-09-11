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

namespace Core\Providers\View {
    
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
                $view = null;
                
                if (config('view.engine') === 'twig') {
                    $view = new Twig(config('view.path.folder'), [
                        'debug' => config('view.debug'),
                        'charset' => 'UTF-8',
                        'cache' => (config('view.cache') && config('app.environment') === 'production') ? config('view.path.compiled') : false,
                        'auto_reload' => true,
                    ]);
                }
                
                if (config('view.engine') === 'php') {
                    $view = new Php(config('view.path.folder'));
                }
                
                return $view;
            };
            
            /**
             * Register view in mail
             *
             * @return \Core\Providers\View\Twig
             */
            $this->container['view-mail'] = function () {
                return new Twig(APP_FOLDER.'/resources/mail', [
                    'charset' => 'UTF-8',
                    'cache' => false,
                    'auto_reload' => true,
                ]);
            };
        }
    }
}
