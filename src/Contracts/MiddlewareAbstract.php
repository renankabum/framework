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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;

/**
 * Class MiddlewareAbstract
 *
 * @package Core\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 *
 * @property \Core\Providers\Hash\BcryptHasher     hash
 * @property \Core\Providers\Session\Session       session
 * @property \Core\Providers\Mailer\Mailer         mailer
 * @property \Core\Providers\Encryption\Encryption encryption
 * @property \Core\Database\Create                 create
 * @property \Core\Database\Read                   read
 * @property \Core\Database\Update                 update
 * @property \Core\Database\Delete                 delete
 * @property \Slim\Router                          router
 * @property \Core\Providers\View\Twig\Twig        view
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
