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

use Slim\Container;

/**
 * Class TwigExtension
 *
 * @package Navegarte\Providers\View
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class TwigExtension extends \Twig_Extension
{
  /**
   * @var \Slim\Container
   */
  protected $container;
  
  /**
   * ViewHelper constructor.
   *
   * @param \Slim\Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }
  
  /**
   * Get class name
   *
   * @return string
   */
  public function getName()
  {
    return __CLASS__;
  }
  
  /**
   * Returns a list of functions to add to the existing list.
   *
   * @return array
   */
  public function getFunctions()
  {
    return [
      new \Twig_SimpleFunction(
        'config', [
          $this,
          'getConfig',
        ]
      ),
      new \Twig_SimpleFunction(
        'asset', [
          $this,
          'getAsset',
        ]
      ),
    ];
  }
  
  /**
   * Get config
   *
   * @param string $name
   * @param null   $default
   *
   * @return array|int|string
   */
  public function getConfig($name, $default = null)
  {
    return config($name, $default);
  }
  
  /**
   * @param string $path
   *
   * @return mixed
   */
  public function getAsset($path)
  {
    return asset($path);
  }
}
