<?php

/**
 * NAVEGARTE Networks
 *
 * @package   FrontEnd
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Providers\View;

use Navegarte\Providers\View\Contracts\BaseView;
use Navegarte\Providers\View\TwigExtension as ExtensionCore;
use Slim\Views\Twig as TwigSlim;
use Slim\Views\TwigExtension as ExtensionSlim;

/**
 * Class Twig
 *
 * @package Navegarte\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Twig extends BaseView
{
  /**
   * Get engine template <b>TWIG</b>
   *
   * @return \Slim\Views\Twig
   */
  public function register()
  {
    $twig = new TwigSlim(config('view.path.folder'), [
      'debug' => config('view.debug', false),
      'charset' => 'UTF-8',
      'cache' => (config('view.cache', false) ? config('view.path.compiled') . '/twig' : false),
      'auto_reload' => true,
    ]);
  
    $uri = rtrim(str_ireplace('index.php', '', $this->request->getUri()->getBasePath()), '/');
    
    $twig->addExtension(new \Twig_Extension_Debug());
    $twig->addExtension(new ExtensionSlim($this->router, $uri));
    $twig->addExtension(new ExtensionCore($this->container));
    
    return $twig;
  }
}
