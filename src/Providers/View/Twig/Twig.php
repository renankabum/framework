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

namespace Core\Providers\View\Twig;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TwigProvider
 *
 * @package Core\Providers\View\Engine
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Twig
{
    /**
     * @var \Twig_Loader_Filesystem
     */
    protected $loader;
    
    /**
     * @var \Twig_Environment
     */
    protected $environment;
    
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;
    
    /**
     * TwigProvider constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string|array                      $path
     * @param array                             $settings
     */
    public function __construct(ContainerInterface $container, $path, array $settings = [])
    {
        $this->container = $container;
        $this->loader = $this->createLoader(is_string($path) ? [$path] : $path);
        $this->environment = new \Twig_Environment($this->loader, $settings);
        
        /**
         * Add default extension debug
         */
        $this->addExtension(new \Twig_Extension_Debug());
        $this->addExtension(new TwigExtension($this->container));
    }
    
    /**
     * Render template views
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $template
     * @param array                               $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render(ResponseInterface $response, $template, array $data = [])
    {
        $response->getBody()->write($this->fetch($template, $data));
        
        return $response;
    }
    
    /**
     * Add new extension
     *
     * @param \Twig_ExtensionInterface $extension
     *
     * @return $this
     */
    public function addExtension(\Twig_ExtensionInterface $extension)
    {
        $this->environment->addExtension($extension);
        
        return $this;
    }
    
    /**
     * Add new function
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     */
    public function addFunction($name, callable $callable, array $options = [])
    {
        $this->environment->addFunction(new \Twig_SimpleFunction($name, $callable, $options));
        
        return $this;
    }
    
    /**
     * Add new filter
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     */
    public function addFilter($name, callable $callable, array $options = [])
    {
        $this->environment->addFilter(new \Twig_SimpleFilter($name, $callable, $options));
        
        return $this;
    }
    
    /**
     * Add new global
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addGlobal($name, $value)
    {
        $this->environment->addGlobal($name, $value);
        
        return $this;
    }
    
    /**
     * Get instanceof TwigProvider load file
     *
     * @return \Twig_Loader_Filesystem
     */
    public function getLoader()
    {
        return $this->loader;
    }
    
    /**
     * Get instanceof TwigProvider
     *
     * @return \Twig_Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
    
    /**
     * @param array $paths
     *
     * @return \Twig_Loader_Filesystem
     */
    private function createLoader(array $paths)
    {
        $loader = new \Twig_Loader_Filesystem();
        
        foreach ($paths as $namespace => $path) {
            if (is_string($namespace)) {
                $loader->setPaths($path, $namespace);
            } else {
                $loader->addPath($path);
            }
        }
        
        return $loader;
    }
    
    /**
     * @param string $template
     * @param array  $data
     *
     * @return string
     */
    private function fetch($template, array $data)
    {
        $data = array_merge($this->variables, $data);
        
        return $this->environment->loadTemplate($template)->render($data);
    }
}
