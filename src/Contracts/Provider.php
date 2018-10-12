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
    use Slim\Container;
    
    /**
     * Class Provider
     *
     * @property \Slim\Collection                      settings
     * @property \Slim\Http\Environment                environment
     * @property \Slim\Http\Request                    request
     * @property \Slim\Http\Response                   response
     * @property \Slim\Router                          router
     *
     * @property \Core\Providers\View\Twig             view
     * @property \Core\Providers\Session\Session       session
     * @property \Core\Providers\Session\Flash         flash
     * @property \Core\Providers\Mailer\Mailer         mailer
     * @property \Core\Providers\Hash\Bcrypt           hash
     * @property \Core\Providers\Hash\Argon            argon
     * @property \Core\Providers\Encryption\Encryption encryption
     * @property \Core\Providers\Jwt\Jwt               jwt
     *
     * @property \Core\Database\Connect                db
     * @property \Core\Database\Statement\Create       create
     * @property \Core\Database\Statement\Read         read
     * @property \Core\Database\Statement\Update       update
     * @property \Core\Database\Statement\Delete       delete
     *
     * @package Core\Contracts
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Provider
    {
        /**
         * @var \Slim\Container
         */
        protected $container;
        
        /**
         * Provider constructor.
         *
         * @param \Slim\Container $container
         */
        public function __construct(Container $container)
        {
            $this->container = $container;
        }
        
        /**
         * Registra novo(s) serviços. (container)
         *
         * @return void
         */
        abstract public function register();
        
        /**
         * Registra outros serviços no escopo do provider
         *
         * @return void
         */
        public function boot()
        {
        }
        
        /**
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            if (App::getInstance()->resolve($name)) {
                return App::getInstance()->resolve($name);
            }
        }
    }
}
