<?php

/**
 * Core Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - Core
 */

namespace Core\Contracts;

use Core\App;

/**
 * Class RouteAbstract
 *
 * @package Core
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class RouteAbstract
{
    /**
     * @var \Core\App
     */
    protected $app;
    
    /**
     * RouteAbstract constructor.
     *
     * @param \Core\App $app
     */
    public function __construct(App $app = null)
    {
        if (is_null($app)) {
            $app = App::getInstance();
        }
        
        $this->app = $app;
    }
    
    /**
     * Create new routers
     *
     * @return mixed
     */
    abstract public function create();
}
