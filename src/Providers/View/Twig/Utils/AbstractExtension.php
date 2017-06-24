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

namespace Navegarte\Providers\View\Twig\Utils;

use Psr\Container\ContainerInterface;

/**
 * Class AbstractExtension
 *
 * @package Navegarte\Providers\View\TwigProvider\Utils
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class AbstractExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    use TraitExtension;
    
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;
    
    /**
     * AbstractExtension constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }
    
    /**
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }
    
    /**
     * @return array|\Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }
    
    /**
     * @return array|\Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }
    
    // METHODS MAGICS
    
    /**
     * @param string $name
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
