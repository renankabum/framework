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

/**
 * Class Model
 *
 * @property \Core\Providers\Hash\BcryptHasher     hash
 * @property \Core\Providers\Session\Session       session
 * @property \Core\Providers\Mailer\Mailer         mailer
 * @property \Core\Providers\Encryption\Encryption encryption
 * @property \Core\Providers\View\Twig\Twig        view
 *
 * @package Core\Contracts
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
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
    }
    
    /**
     * Cria o registro de forma simples
     * Retorna o ID inserido
     *
     * @param string $table
     * @param array  $data
     *
     * @return int|bool
     */
    public function create($table, array $data)
    {
        $create = $this->container['create']->exec($table, $data);
        
        if (!$create->getResult()) {
            return false;
        }
        
        return $create->getResult();
    }
    
    /**
     * Cria vários registro passando um array multimensional
     *
     * @param string $table
     * @param array  $data
     *
     * @throws int
     */
    public function createMultiple($table, array $data)
    {
        return $this->container['create']->execMulti($table, $data);
    }
    
    /**
     * Executa a query passando toda ela
     *
     * @param string $query
     * @param string $places
     *
     * @return array|bool
     */
    public function read($query, $places = null)
    {
        $read = $this->container['read']->query($query, $places);
        
        if (!$read->getResult()) {
            return false;
        }
        
        return $read->getResult();
    }
    
    /**
     * Executa o update no banco de dados simplificado
     *
     * @param string      $table
     * @param array       $data
     * @param string      $terms
     * @param string|null $places
     *
     * @return int|bool
     */
    public function update($table, array $data, $terms, $places = null)
    {
        $update = $this->container['update']->exec($table, $data, $terms, $places);
        
        if ($update->getRowCount() <= 0) {
            return $update->getRowCount();
        }
        
        return $update->getResult();
    }
    
    /**
     * Executa a query
     *
     * @param string $table
     * @param string $terms
     * @param string $places
     *
     * @return int|bool
     */
    public function delete($table, $terms, $places = null)
    {
        $delete = $this->container['delete']->exec($table, $terms, $places);
        
        if ($delete->getRowCount() <= 0) {
            return $delete->getRowCount();
        }
        
        return $delete->getResult();
    }
    
    /**
     * Pega os provider cadastrados
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
    }
}
