<?php

/**
 * Core <https://www.vagnercardosoweb.com.br/>
 *
 * @package   Core
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso
 */

namespace Core\Database;

/**
 * Class Read
 *
 * @package App\Database
 */
final class Update
{
    /**
     * @var string
     */
    private $table;
    
    /**
     * @var array
     */
    private $data;
    
    /**
     * @var string
     */
    private $terms;
    
    /**
     * @var string
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
     * @param array       $data
     * @param string      $terms
     * @param string|null $places
     */
    public function exec($table, array $data, $terms, $places = null)
    {
        $this->table = (string)$table;
        $this->data = (array)$data;
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
            
            throw new \Exception("Update:: {$e->getMessage()}");
        }
    }
    
    /**
     * Cria a syntax da query para prepared statement
     */
    private function syntax()
    {
        $data = [];
        
        foreach ($this->data as $index => $value) {
            $data[] = "{$index} = :{$index}";
        }
        
        $data = implode(', ', $data);
        $this->statement = "UPDATE {$this->table} SET {$data} {$this->terms}";
    }
    
    /**
     * Obtém o PDO e Prepara a Query
     */
    private function connect()
    {
        $this->statement = $this->conn->prepare($this->statement);
        
        /**
         * Verifica se os dados passados estão NULL e se estiver seta como vázio
         */
        foreach ($this->data as $index => $place) {
            $this->data[$index] = ($place == '' ? null : $place);
        }
        
        /**
         * Percore os dados e places para montar os binds
         */
        $merging = $this->data;
        
        if ($this->places) {
            $merging = array_merge($this->data, $this->places);
        }
        
        foreach ($merging as $index => $place) {
            $this->statement->bindValue(":{$index}", $place, (is_int($place) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
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
