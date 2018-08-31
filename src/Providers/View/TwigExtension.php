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
    
    /**
     * Class TwigExtension
     *
     * @package Core\Providers\TwigProvider
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     *
     * @property \Slim\Router                    router
     * @property \Slim\Http\Request              request
     * @property \Core\Providers\Session\Session session
     * @property \Core\Providers\Session\Flash   flash
     */
    final class TwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
    {
        /**
         * Get class name
         *
         * @return string
         */
        public function getName()
        {
            return 'vcweb_twig';
        }
        
        /**
         * Returns a list of functions to add to the existing list.
         *
         * @return array
         */
        public function getFunctions()
        {
            return [
                new \Twig_SimpleFunction('path_for', 'path_for'),
                new \Twig_SimpleFunction('config', 'config'),
                new \Twig_SimpleFunction('asset', 'asset', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('asset_source', 'asset_source', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('has_route', 'has_route'),
                new \Twig_SimpleFunction('is_route', 'is_route'),
                new \Twig_SimpleFunction('flash', [$this, 'flash'], ['is_safe' => ['html']]),
            ];
        }
        
        /**
         * Returns a list of globals to add to the existing list.
         *
         * @return array
         */
        public function getGlobals()
        {
            return [
                'session' => app()->resolve('session'),
            ];
        }
        
        // FUNCTIONS TWIG
        
        /**
         * @param string $key
         *
         * @return mixed
         */
        public function flash($key = null)
        {
            return app()
                ->resolve('flash')
                ->get($key);
        }
    }
}
