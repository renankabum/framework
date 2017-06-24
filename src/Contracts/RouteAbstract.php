<?php

/**
 * NAVEGARTE Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Contracts;

use Navegarte\App;

/**
 * Class RouteAbstract
 *
 * @package VCWeb
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class RouteAbstract
{
    /**
     * @var \Navegarte\App
     */
    protected $app;
    
    /**
     * RouteAbstract constructor.
     *
     * @param \Navegarte\App $app
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
