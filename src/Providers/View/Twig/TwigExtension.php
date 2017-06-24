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

namespace Navegarte\Providers\View\Twig;

use Psr\Container\ContainerInterface;

/**
 * Class TwigExtension
 *
 * @package Navegarte\Providers\View\TwigProvider
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 *
 * @property \Slim\Router                         router
 * @property \Slim\Http\Request                   request
 * @property \Navegarte\Providers\Session\Session session
 */
final class TwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var \Slim\Container
     */
    protected $container;
    
    /**
     * ViewHelper constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
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
            new \Twig_SimpleFunction('path_for', [$this, 'path_for']),
            new \Twig_SimpleFunction('base_url', [$this, 'base_url']),
            new \Twig_SimpleFunction('config', [$this, 'config']),
            new \Twig_SimpleFunction('asset', [$this, 'asset']),
            new \Twig_SimpleFunction('asset_source', [$this, 'asset_source']),
            new \Twig_SimpleFunction('is_route', [$this, 'is_route']),
            new \Twig_SimpleFunction('is_route_active', [$this, 'is_route_active']),
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
            'session' => $this->session
        ];
    }
    
    // FUNCTIONS TWIG
    
    /**
     * Build the path for a named route including the base path
     *
     * @param string $name
     * @param array  $data
     * @param array  $queryParams
     *
     * @return string
     */
    public function path_for($name, array $data = [], array $queryParams = [])
    {
        return $this->router->pathFor($name, $data, $queryParams);
    }
    
    /**
     * Get base Url
     *
     * @return string
     */
    public function base_url()
    {
        $basePath = rtrim(str_ireplace('index.php', '', $this->request->getUri()->getBasePath()), '/');
        
        if (is_string($basePath)) {
            return $basePath;
        }
        
        if (method_exists($basePath, 'getBaseUrl')) {
            return $basePath->getBaseUrl();
        }
    }
    
    /**
     * Get config
     *
     * @param string          $name
     * @param null|string|int $default
     *
     * @return mixed
     */
    public function config($name, $default = null)
    {
        return config($name, $default);
    }
    
    /**
     * Get path asset
     *
     * @param string $path
     *
     * @return mixed
     */
    public function asset($path)
    {
        return asset($path);
    }
    
    /**
     * Get source code asset
     *
     * @param string|array $path
     *
     * @return bool|string
     */
    public function asset_source($path)
    {
        return asset_source($path);
    }
    
    /**
     * Checks if the route is active.
     *
     * @param string $name
     *
     * @return bool
     */
    public function is_route($name)
    {
        return $this->router->pathFor($name) === $this->request->getUri()->getPath();
    }
    
    /**
     * Checks if the route is active and adds the 'active' class
     *
     * @param $name
     *
     * @return bool
     */
    public function is_route_active($name)
    {
        if ($this->router->pathFor($name) === $this->request->getUri()->getPath()) {
            return 'active';
        }
        
        return false;
    }
    
    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
    }
}
