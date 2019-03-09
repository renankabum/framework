<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 23/01/19 Vagner Cardoso
 */

namespace Core\Contracts {
    
    use Core\App;
    use Slim\Container as SlimContainer;
    
    /**
     * Class Container
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
     * @property \Core\Providers\Logger\Logger logger
     * @property \Core\Providers\Event\Event event
     *
     * @property \Core\Providers\Database\Database db
     *
     * @package Core\Contracts
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Container
    {
        /**
         * @var \Slim\Container
         */
        protected $container;
        
        /**
         * Container constructor.
         *
         * @param \Slim\Container|null $container
         */
        public function __construct(SlimContainer $container = null)
        {
            $this->container = ($container ?: App::getInstance()->getContainer());
        }
        
        /**
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
