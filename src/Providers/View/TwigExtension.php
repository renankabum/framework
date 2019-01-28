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
    
    use Core\App;
    
    /**
     * Class TwigExtension
     *
     * @package Core\Providers\TwigProvider
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     *
     * @property \Slim\Router router
     * @property \Slim\Http\Request request
     * @property \Core\Providers\Session\Session session
     * @property \Core\Providers\Session\Flash flash
     */
    class  TwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
                new \Twig_SimpleFunction('path_for', 'path_for', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('config', 'config', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('asset', 'asset', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('asset_source', 'asset_source', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('has_route', 'has_route', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('is_route', 'is_route', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('dd', 'dd', ['is_safe' => ['all']]),
                new \Twig_SimpleFunction('has_container', [$this, 'has_container']),
            ];
        }
        
        /**
         * Returns a list of globals to add to the existing list.
         *
         * @return array
         */
        public function getGlobals()
        {
            return [];
        }
        
        /**
         * @param string $name
         *
         * @return mixed
         */
        public function has_container($name)
        {
            return App::getInstance()->resolve($name);
        }
    }
}
