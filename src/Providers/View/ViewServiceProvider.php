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

namespace Navegarte\Providers\View;

use Navegarte\Contracts\ServiceProviderAbstract;
use Navegarte\Providers\View\Twig\TwigExtension;
use Slim\Container;

/**
 * Class ViewServiceProvider
 *
 * @package Navegarte\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class ViewServiceProvider extends ServiceProviderAbstract
{
    /**
     * Registers services on the given container.
     *
     * @param \Slim\Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        $container['view'] = function () use ($container) {
            
            $engineObject = null;
            
            switch (config('view.engine')) {
                case 'php':
                    $engineObject = (new PhpProvider($container))->register();
                    break;
                case 'blade':
                    $engineObject = (new BladeProvider($container))->register();
                    break;
                case 'twig':
                    $engineObject = (new TwigProvider($container))->register();
                    break;
            }
            
            if (!is_object($engineObject)) {
                throw new \Exception('Erro processamento da view.', E_USER_ERROR);
            }
            
            return $engineObject;
        };
        
        /**
         * Register view in mail
         *
         * @return \Twig_Environment
         */
        $container['view.mail'] = function () use ($container) {
            $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(config('view.path.folder')));
            $twig->addExtension(new TwigExtension($container));
            
            return $twig;
        };
    }
}
