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
    use Slim\Exception\NotFoundException;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class Controller
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
         * @var string|array
         */
        private $params;
        
        /**
         * @var \Slim\Container
         */
        protected $container;
        
        /**
         * BaseController constructor.
         *
         * @param \Slim\Http\Request  $request
         * @param \Slim\Http\Response $response
         * @param string|array        $params
         * @param \Slim\Container     $container
         */
        public function __construct(Request $request, Response $response, $params, Container $container)
        {
            $this->request = $request;
            $this->response = $response;
            $this->params = $params;
            $this->container = $container;
            
            $this->boot();
        }
        
        /**
         * Pega os parametros get, post etc.
         *
         * @param null|string $name
         *
         * @return array|mixed
         */
        public function param($name = null)
        {
            return input($name);
        }
        
        /**
         * Retorna a view e popula seus dados dentro dela
         *
         * @param string $view
         * @param array  $array
         * @param int    $code
         *
         * @return Response
         */
        public function view($view, array $array = array(), $code = null)
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
         * @param array  $context
         * @param string $type
         * @param string $file
         *
         * @return mixed
         */
        public function logger($message, array $context = array(), $type = 'info', $file = null)
        {
            return logger($message, $context, $type, $file);
        }
        
        /**
         * Redireciona passando o nome da rota
         * e seus parametros e querys
         *
         * @param null|string $name
         * @param array       $data
         * @param array       $queryParams
         * @param string      $hash
         *
         * @return \Slim\Http\Response
         */
        public function redirect($name = null, array $data = [], array $queryParams = [], $hash = null)
        {
            return redirect($name, $data, $queryParams, $hash);
        }
        
        /**
         * Retorna a URL da rota passando o name
         * dado na rota
         *
         * @param null   $name
         * @param array  $data
         * @param array  $queryParams
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
         * @param int   $status
         *
         * @return \Slim\Http\Response
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
         * Inicializa junto com o controller
         */
        public function boot()
        {
        }
        
        /**
         * @param string $name
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
