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

namespace Core\Providers\View;

use Core\Contracts\ServiceProviderAbstract;
use Core\Providers\View\Twig\TwigExtension;
use Slim\Container;

/**
 * Class ViewServiceProvider
 *
 * @package Core\Providers\View
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
                    $engineObject = new PhpProvider($container);
                    break;
                case 'twig':
                    $engineObject = new TwigProvider($container);
                    break;
            }
            
            if (!is_object($engineObject)) {
                throw new \Exception('Erro processamento da view.', E_USER_ERROR);
            }
    
            return $engineObject->register();
        };
        
        /**
         * Register view in mail
         *
         * @return \Twig_Environment
         */
        $container['view.mail'] = function () use ($container) {
            $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(APP_FOLDER . '/resources/mail'));
            $twig->addExtension(new TwigExtension($container));
            
            return $twig;
        };
    }
}
