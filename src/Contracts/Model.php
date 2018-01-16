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

    /**
     * Class Model
     *
     * @package Core\Contracts
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     *
     * @property \Core\Providers\Hash\BcryptHasher        hash
     * @property \Core\Providers\Hash\ArgonHasher         hashArgon
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
     * @property \Slim\Container                          container
     * @property \Slim\Http\Response                      response
     * @property \Slim\Http\Request                       request
     * @property \Slim\Router                             router
     */
    abstract class Model
    {
        /**
         * @var \Slim\Container
         */
        protected $container;

        /**
         * Model constructor.
         */
        public function __construct()
        {
            $this->container = app()->getContainer();

            $this->boot();
        }

        /**
         * Inicializa junto com o Model
         *
         * @return mixed
         */
        protected function boot(){}

        /**
         * Pega os provider cadastrados
         *
         * @param $name
         *
         * @return mixed
         * @throws \Interop\Container\Exception\ContainerException
         */
        public function __get($name)
        {
            if ($this->container->has($name)) {
                return $this->container->get($name);
            }
        }
    }
}
