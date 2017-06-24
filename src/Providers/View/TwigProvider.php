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

use Navegarte\Contracts\ViewAbstract;
use Navegarte\Providers\View\Twig\Twig as TwigEngine;

/**
 * Class TwigProvider
 *
 * @package Navegarte\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class TwigProvider extends ViewAbstract
{
    /**
     * @return \Navegarte\Providers\View\Twig\Twig
     */
    public function register()
    {
        $twig = new TwigEngine(
            $this->container, config('view.path.folder'), [
                'debug' => config('view.debug', false),
                'charset' => 'UTF-8',
                'cache' => (config('view.cache', false) ? config('view.path.compiled') . '/twig' : false),
                'auto_reload' => true,
            ]
        );
        
        return $twig;
    }
}
