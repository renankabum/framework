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

namespace Core\Contracts;

use Slim\Container;

/**
 * Class ViewAbstract
 *
 * @package Core\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 *
 * @property \Core\Providers\Session\Session session
 */
abstract class ViewAbstract
{
    /**
     * @var \Slim\Container $container
     */
    protected $container;
    
    /**
     * TwigProvider constructor.
     *
     * @param \Slim\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * Register new view provider
     *
     * @return mixed
     */
    abstract public function register();
    
    /**
     * Get provider
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->container->{$name}) {
            return $this->container->{$name};
        }
    }
}
