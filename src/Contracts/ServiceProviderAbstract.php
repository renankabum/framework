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

namespace Core\Contracts;

use Slim\Container;

/**
 * Class ServiceProviderAbstract
 *
 * @package Core\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class ServiceProviderAbstract
{
    /**
     * Registers services on the given container.
     *
     * @param \Slim\Container $container
     *
     * @return mixed|void
     */
    abstract public function register(Container $container);
    
    /**
     * Register other services, such as middleware etc.
     *
     * @return mixed|void
     */
    public function boot()
    {
    }
}
