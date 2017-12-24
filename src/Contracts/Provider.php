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

    /**
     * Class Provider
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
     *
     * @property \Slim\Http\Response                      response
     * @property \Slim\Http\Request                       request
     * @property \Slim\Router                             router
     */
    abstract class Provider
    {
        /**
         * @var Container
         */
        protected $container;

        /**
         * Provider constructor.
         *
         * @param Container $container
         */
        public function __construct(Container $container)
        {
            $this->container = $container;
        }

        /**
         * Registers services on the given container.
         *
         * @return void
         */
        abstract public function register();

        /**
         * Register other services, such as middleware etc.
         *
         * @return void
         */
        public function boot()
        {
        }

        /**
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
