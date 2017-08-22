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

use Core\Contracts\ViewAbstract;
use Core\Providers\View\Twig\Twig;

/**
 * Class TwigProvider
 *
 * @package Core\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class TwigProvider extends ViewAbstract
{
    /**
     * @return \Core\Providers\View\Twig\Twig
     */
    public function register()
    {
        $twig = new Twig(
            $this->container, config('view.path.folder'), [
                'debug' => config('view.debug', false),
                'charset' => 'UTF-8',
                'cache' => (config('view.cache', false) ? config('view.path.compiled') : false),
                'auto_reload' => true,
            ]
        );
        
        return $twig;
    }
}
