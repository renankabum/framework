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

namespace Navegarte\Contracts;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;

/**
 * Class MiddlewareAbstract
 *
 * @package Navegarte\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 *
 * @property \Navegarte\Providers\Hash\BcryptHasher     hash
 * @property \Navegarte\Providers\Session\Session       session
 * @property \Navegarte\Providers\Mailer\Mailer         mailer
 * @property \Navegarte\Providers\Encryption\Encryption encryption
 * @property \Navegarte\Database\Create                 create
 * @property \Navegarte\Database\Read                   read
 * @property \Navegarte\Database\Update                 update
 * @property \Navegarte\Database\Delete                 delete
 * @property \Slim\Router                               router
 * @property \Navegarte\Providers\View\Twig\Twig        view
 */
abstract class MiddlewareAbstract
{
    /**
     * @var \Slim\Container
     */
    protected $container;
    
    /**
     * BaseMiddleware constructor.
     *
     * @param \Slim\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * Register middleware
     *
     * @param \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response     $response PSR7 response
     * @param callable                                                    $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    abstract public function __invoke(Request $request, Response $response, callable $next);
    
    /**
     * Get property in container
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
