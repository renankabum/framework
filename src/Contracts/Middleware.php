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

namespace Core\Contracts {

    use Slim\Container;
    use Slim\Http\Request;
    use Slim\Http\Response;

    /**
     * Class Middleware
     *
     * @package Core\Contracts
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     *
     * @property \Core\Providers\Hash\BcryptHasher        hash
     * @property \Core\Providers\Session\Session          session
     * @property \Core\Providers\Mailer\Mailer            mailer
     * @property \Core\Providers\Encryption\Encryption    encryption
     * @property \Core\Providers\View\Twig                view
     * @property \Core\Providers\Session\Flash            flash
     *
     * @property \Core\Database\Database|\PDO             db
     * @property \Core\Database\Statement\CreateStatement create
     * @property \Core\Database\Statement\ReadStatement   read
     * @property \Core\Database\Statement\UpdateStatement update
     * @property \Core\Database\Statement\DeleteStatement delete
     * @property \Slim\Http\Response                      response
     * @property \Slim\Http\Request                       request
     * @property \Slim\Router                             router
     */
    abstract class Middleware
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

            $this->boot();
        }

        /**
         * Inicia junto com a middleware
         */
        protected function boot(){}

        /**
         * Register middleware
         *
         * @param \Slim\Http\Request  $request  PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable            $next     Next middleware
         *
         * @return \Slim\Http\Response
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
}
