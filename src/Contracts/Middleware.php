<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Contracts {
    
    use Core\App;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class Middleware
     *
     * @property \Slim\Collection settings
     * @property \Slim\Http\Environment environment
     * @property \Slim\Http\Request request
     * @property \Slim\Http\Response response
     * @property \Slim\Router router
     *
     * @property \Core\Providers\View\Twig view
     * @property \Core\Providers\Session\Session session
     * @property \Core\Providers\Session\Flash flash
     * @property \Core\Providers\Mailer\Mailer mailer
     * @property \Core\Providers\Hash\Bcrypt hash
     * @property \Core\Providers\Encryption\Encryption encryption
     * @property \Core\Providers\Jwt\Jwt jwt
     * @property \Core\Providers\Event\Event event
     *
     * @property \Core\Providers\Database\Database db
     *
     * @package Core\Contracts
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Middleware
    {
        /**
         * Registra uma nova middleware
         *
         * @param \Slim\Http\Request $request PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable $next Next middleware
         *
         * @return \Slim\Http\Response
         */
        abstract public function __invoke(Request $request, Response $response, callable $next);
        
        /**
         * Recupera o container cadastro
         *
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            return App::getInstance()->resolve($name);
        }
    }
}
