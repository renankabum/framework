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

namespace Core\Contracts;

use Carbon\Carbon;
use Slim\Container;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ControllerAbstract
 *
 * @package Core\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 *
 * @property \Core\Providers\Hash\BcryptHasher     hash
 * @property \Core\Providers\Session\Session       session
 * @property \Core\Providers\Mailer\Mailer         mailer
 * @property \Core\Providers\Encryption\Encryption encryption
 * @property \Core\Providers\View\Twig\Twig        view
 *
 * @property \Core\Database\Create                 create
 * @property \Core\Database\Read                   read
 * @property \Core\Database\Update                 update
 * @property \Core\Database\Delete                 delete
 */
abstract class ControllerAbstract
{
    /**
     * @var \Slim\Http\Request
     */
    private $request;
    
    /**
     * @var \Slim\Http\Response
     */
    private $response;
    
    /**
     * @var string|array
     */
    private $params;
    
    /**
     * @var \Slim\Container
     */
    private $container;
    
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
        $params = $this->request->getParams();
        $params = $this->filterParams($params);
        
        if (is_null($name)) {
            return $params;
        }
    
        return $params[$name];
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
     * Pega o indice passado para a luanguage
     * e verifica se existe o cookie `__language__` setado
     * para escolher qual language usar
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public function lang($name = null, $default = null)
    {
        $language = $this->config('app.locale');
        
        $cookie = filter_input(INPUT_COOKIE, '__language__', FILTER_DEFAULT);
        if (isset($cookie) && !empty($cookie)) {
            $decode = $this->encryption->decrypt($cookie);
            
            $language = $decode['flag'];
            $time = Carbon::now()->getTimestamp();
            
            if ($decode['time'] < $time) {
                $language = $this->config('app.locale');
            }
        }
        
        if (!is_null($name)) {
            $name = ".{$name}";
        }
        
        $name = "lang.{$language}{$name}";
        
        return $this->config($name, $default);
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
        $type = strtolower($type);
        $type = strtoupper(substr($type, 0, 1)) . substr($type, 1);
        
        return logger($file)->{"add{$type}"}($message, $context);
    }
    
    /**
     * Redireciona passando o nome da rota
     * e seus parametros e querys
     *
     * @param null|string $name
     * @param array       $data
     * @param array       $queryParams
     *
     * @return \Slim\Http\Response
     */
    public function redirect($name = null, array $data = [], array $queryParams = [])
    {
        $name = !is_null($name) ? $name : 'home';
        
        return $this->response->withRedirect($this->pathFor($name, $data, $queryParams));
    }
    
    /**
     * Retorna a URL da rota passando o name
     * dado na rota
     *
     * @param null  $name
     * @param array $data
     * @param array $queryParams
     *
     * @return string
     */
    public function pathFor($name = null, array $data = [], array $queryParams = [])
    {
        $name = !is_null($name) ? $name : 'home';
    
        return $this->getRouter()->pathFor($name, $data, $queryParams);
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
        return $this->response->withJson($data, $status, JSON_PRETTY_PRINT);
    }
    
    /**
     * Verifica se o metódo acessado e POST
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->request->isPost();
    }
    
    /**
     * Verifica se o metódo acessado e GET
     *
     * @return bool
     */
    public function isGet()
    {
        return $this->request->isGet();
    }
    
    /**
     * Verifica se o metódo acessado e XMLHttpRequest (ajax)
     *
     * @return bool
     */
    public function isXhr()
    {
        return $this->request->isXhr();
    }
    
    /**
     * Pega as headers passada na requisição
     *
     * @return array|\string[][]
     */
    public function getHeaders()
    {
        return $this->request->getHeaders();
    }
    
    /**
     * Recupera a váriavel $_SERVER[]
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->request->getServerParams();
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
    
    /**
     * Recupera a classe route
     *
     * @return \Slim\Router|\Slim\Route
     */
    public function getRouter()
    {
        return $this->container['router'];
    }
    
    /**
     * Filtra os parametros passados por GET E POST
     *
     * @param array $params
     *
     * @return array
     */
    private function filterParams(array $params)
    {
        $filtered = [];
        
        foreach ((array) $params as $key => $param) {
            if (is_null($param)) {
                continue;
            }
            
            if (is_array($param)) {
                $filtered[$key] = $this->filterParams($param);
            } else {
                $filter = (is_int($param) ? FILTER_SANITIZE_NUMBER_INT : (is_float($param) ? FILTER_SANITIZE_NUMBER_FLOAT : FILTER_SANITIZE_STRING));
                
                $filtered[$key] = strip_tags(trim(filter_var($param, $filter)));
            }
        }
        
        return $filtered;
    }
}
