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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;
use Slim\Exception\NotFoundException;

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
 * @property \Core\Database\Create                 create
 * @property \Core\Database\Read                   read
 * @property \Core\Database\Update                 update
 * @property \Core\Database\Delete                 delete
 * @property \Core\Providers\View\Twig\Twig        view
 * @property \Core\Helpers\Config                  config
 */
abstract class ControllerAbstract
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request
     */
    protected $request;
    
    /**
     * @var \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    protected $response;
    
    /**
     * @var string|array
     */
    protected $params;
    
    /**
     * @var \Slim\Container
     */
    protected $container;
    
    /**
     * BaseController constructor.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param string|array                             $params
     * @param \Slim\Container                          $container
     */
    public function __construct(Request $request, Response $response, $params, Container $container)
    {
        $this->request = $request;
        $this->response = $response;
        $this->params = $params;
        $this->container = $container;
    }
    
    /**
     * @param null|string $name
     *
     * @return array|mixed
     */
    public function param($name = null)
    {
        if (is_null($name)) {
            return $this->request->getParams();
        }
        
        return $this->request->getParam($name);
    }
    
    /**
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
     * @param null  $name
     * @param array $data
     * @param array $queryParams
     *
     * @return string
     */
    public function pathFor($name = null, array $data = [], array $queryParams = [])
    {
        $name = !is_null($name) ? $name : 'home';
        
        return $this->router()->pathFor($name, $data, $queryParams);
    }
    
    /**
     * @param mixed $data
     * @param bool  $aJson
     * @param int   $status
     *
     * @return \Slim\Http\Response
     */
    public function json($data, $aJson = false, $status = 200)
    {
        if (!empty($aJson)) {
            $data = [$data];
        }
        
        return $this->response->withJson($data, $status, JSON_PRETTY_PRINT);
    }
    
    /**
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
     * @return \Slim\Router|\Slim\Route
     */
    protected function router()
    {
        return $this->container['router'];
    }
}
