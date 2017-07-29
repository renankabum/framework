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

namespace Core\Database;

/**
 * Class Delete
 *
 * @package App\Database
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Delete
{
    /**
     * @var string
     */
    private $table;
    
    /**
     * @var string
     */
    private $terms;
    
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
     * @param string      $terms
     * @param string|null $places
     */
    public function exec($table, $terms, $places = null)
    {
        $this->table = (string)$table;
        $this->terms = (string)$terms;
        
        if (!empty($places)) {
            parse_str($places, $this->places);
        }
        $this->execute();
    }
    
    /**
     * Obtém a conexão a syntax e executa a query
     */
    private function execute()
    {
        try {
            $this->syntax();
            $this->connect();
            $this->statement->execute();
            $this->result = true;
        } catch (\PDOException $e) {
            $this->result = null;
            
            throw new \Exception("Delete:: {$e->getMessage()}");
        }
    }
    
    /**
     * Cria a syntax da query para prepared statement
     */
    private function syntax()
    {
        $this->statement = "DELETE FROM {$this->table} {$this->terms}";
    }
    
    /**
     * Obtém o PDO e Prepara a Query
     */
    private function connect()
    {
        $this->statement = $this->conn->prepare($this->statement);
        
        /**
         * Percore os dados e places para montar os binds
         */
        if ($this->places) {
            foreach ($this->places as $index => $place) {
                $this->statement->bindValue(":{$index}", $place, (is_int($place) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
            }
        }
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
