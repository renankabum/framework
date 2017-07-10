<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Providers\View\Twig\Utils;

/**
 * Trait TraitExtension
 *
 * @package Core\Providers\View\TwigProvider\Utils
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
trait TraitExtension
{
    /**
     * @var array
     */
    protected $globals = [];
    
    /**
     * @var array
     */
    protected $functions = [];
    
    /**
     * @var array
     */
    protected $filters = [];
    
    /**
     * Add new function in twig template
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     */
    public function addFunction(string $name, callable $callable, array $options = [])
    {
        $this->functions[] = new \Twig_SimpleFunction($name, $callable, $options);
        
        return $this;
    }
    
    /**
     * Add new filter in twig template
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     */
    public function addFilter(string $name, callable $callable, array $options = [])
    {
        $this->filters[] = new \Twig_SimpleFilter($name, $callable, $options);
        
        return $this;
    }
    
    /**
     * Add new globals in template
     *
     * @param string $name
     * @param mixed  $var
     *
     * @return $this
     */
    public function addGlobal(string $name, $var)
    {
        if (array_key_exists($name, $this->globals)) {
            return @trigger_error('Essa váriavel global já foi adicionada.', E_USER_WARNING);
        }
        
        $this->globals[$name] = $var;
        
        return $this;
    }
}
