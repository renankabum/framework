<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Providers\View {

    use Core\Contracts\Provider;

    /**
     * Class ViewProvider
     *
     * @package Core\Providers\View
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class ViewProvider extends Provider
    {
        /**
         * Registers services on the given container.
         *
         * @return void
         */
        public function register()
        {
            $this->container['view'] = function () {
                $config = object_set(config('view'));

                $view = null;

                if ($config->engine === 'twig') {
                    $view = new Twig(
                        $config->path->folder, [
                            'debug' => $config->debug,
                            'charset' => 'UTF-8',
                            'cache' => $config->cache ? $config->path->compiled : false,
                            'auto_reload' => true,
                        ]
                    );
                }

                if ($config->engine === 'php') {
                    $view = new Php($config->path->folder);
                }

                return $view;
            };

            /**
             * Register view in mail
             *
             * @return \Twig_Environment
             */
            $this->container['mailView'] = function () {
                $mailView = new \Twig_Environment(new \Twig_Loader_Filesystem(APP_FOLDER . '/resources/mail'));
                $mailView->addExtension(new TwigExtension());

                return $mailView;
            };
        }
    }
}
