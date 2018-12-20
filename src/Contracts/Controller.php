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
    use Slim\Exception\NotFoundException;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class Controller
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
    abstract class Controller
    {
        /**
         * @var \Slim\Http\Request
         */
        protected $request;
        
        /**
         * @var \Slim\Http\Response
         */
        protected $response;
        
        /**
         * @var \Slim\Container
         */
        protected $container;
        
        /**
         * Controller constructor.
         *
         * @param \Slim\Http\Request $request
         * @param \Slim\Http\Response $response
         * @param \Slim\Container $container
         */
        public function __construct(Request $request, Response $response, Container $container)
        {
            $this->request = $request;
            $this->response = $response;
            $this->container = $container;
            
            $this->boot();
        }
        
        /**
         * Inicia junto com o __construct da classe pai
         */
        protected function boot()
        {
        }
        
        /**
         * Pega os parametros get, post etc.
         *
         * @param string $name
         *
         * @return array|mixed
         */
        public function param($name = null)
        {
            return params($name);
        }
        
        /**
         * Retorna a view e popula seus dados dentro dela
         *
         * @param string $view
         * @param array $array
         * @param int $code
         *
         * @return Response
         */
        public function view($view, array $array = [], $code = null)
        {
            return view($view, $array, $code);
        }
        
        /**
         * Pega as configurações do sistema
         *
         * @param string $name
         * @param string $default
         *
         * @return mixed
         */
        public function config($name = null, $default = null)
        {
            return config($name, $default);
        }
        
        /**
         * Realiza a salvação de logs no sistema
         *
         * @param string $message
         * @param array $context
         * @param string $file
         * @param string $type
         *
         * @return mixed
         */
        public function logger($message, array $context = [], $file = null, $type = 'info')
        {
            return logger($message, $context, $type, $file);
        }
        
        /**
         * Redireciona passando o nome da rota
         * e seus parametros e querys
         *
         * @param string $name
         * @param array $data
         * @param array $queryParams
         * @param string $hash
         *
         * @return Response
         */
        public function redirect($name = null, array $data = [], array $queryParams = [], $hash = null)
        {
            return redirect($name, $data, $queryParams, $hash);
        }
        
        /**
         * Retorna a URL da rota passando o name
         * dado na rota
         *
         * @param string $name
         * @param array $data
         * @param array $queryParams
         * @param string $hash
         *
         * @return string
         */
        public function pathFor($name = null, array $data = [], array $queryParams = [], $hash = null)
        {
            return path_for($name, $data, $queryParams, $hash);
        }
        
        /**
         * Retorna um json populado
         *
         * @param mixed $data
         * @param int $status
         *
         * @return Response
         */
        public function json($data, $status = 200)
        {
            return json($data, $status);
        }
        
        /**
         * Retorna página offline
         *
         * @throws \Slim\Exception\NotFoundException
         */
        public function notFound()
        {
            throw new NotFoundException($this->request, $this->response);
        }
        
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
