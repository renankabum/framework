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

namespace Navegarte\Database;

/**
 * Class Read
 *
 * @package Navegarte\Database
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Read
{
    /**
     * @var string
     */
    private $select;
    
    /**
     * @var array
     */
    private $places;
    
    /**
     * @var array
     */
    private $result;
    
    /**
     * @var \PDOStatement
     */
    private $statement;
    
    /**
     * @var \PDO
     */
    private $conn;
    
    /**
     * Read constructor.
     *
     * Obtém a conexão do banco de dados
     */
    public function __construct()
    {
        $this->conn = Connect::boot();
    }
    
    /**
     *
     *
     * @param string      $table
     * @param string|null $terms
     * @param string|null $places
     */
    public function exec($table, $terms = null, $places = null)
    {
        if (!empty($places)) {
            parse_str($places, $this->places);
        }
        
        $this->select = "SELECT * FROM {$table} {$terms}";
        $this->execute();
    }
    
    /**
     * Obtém a conexão a syntax e executa a query
     */
    private function execute()
    {
        try {
            $this->connect();
            $this->syntax();
            $this->statement->execute();
            $this->result = $this->statement->fetchAll();
        } catch (\PDOException $e) {
            $this->result = null;
            
            throw new \Exception("Read:: {$e->getMessage()}");
        }
    }
    
    /**
     * Obtém o PDO e Prepara a Query
     */
    private function connect()
    {
        
        $this->statement = $this->conn->prepare($this->select);
    }
    
    /**
     * Cria a syntax da query para prepared statement
     */
    private function syntax()
    {
        if ($this->places) {
            foreach ($this->places as $index => $place) {
                if ($index == 'limit' || $index == 'offset') {
                    $place = (int)$place;
                }
                
                $this->statement->bindValue(":{$index}", $place, (is_int($place) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
            }
        }
    }
    
    /**
     *
     *
     * @param string      $query
     * @param string|null $places
     */
    public function query($query, $places = null)
    {
        $this->select = (string)$query;
        
        if (!empty($places)) {
            parse_str($places, $this->places);
        }
        
        $this->execute();
    }
    
    
    // Methods privates
    
    /**
     *
     *
     * @param string $places
     */
    public function setPlaces($places)
    {
        parse_str($places, $this->places);
        $this->execute();
    }
    
    /**
     *
     *
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     *
     *
     * @return int
     */
    public function getRowCount()
    {
        if ($this->statement->rowCount() == -1) {
            return count($this->statement->fetchAll());
        }
        
        return $this->statement->rowCount();
    }
}
