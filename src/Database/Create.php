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
 * Class Create
 *
 * @package App\Database
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class Create
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
     * Create constructor.
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
     * @param string $table
     * @param array  $data
     */
    public function exec($table, array $data)
    {
        $this->table = (string)$table;
        $this->data = (array)$data;
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
            $this->result = $this->conn->lastInsertId();
        } catch (\PDOException $e) {
            $this->result = null;
            
            throw new \Exception("Create:: {$e->getMessage()}");
        }
    }
    
    /**
     * Cria a syntax da query para prepared statement
     */
    private function syntax()
    {
        $fields = implode(', ', array_keys($this->data));
        $places = ':' . implode(', :', array_keys($this->data));
        
        $this->statement = "INSERT INTO {$this->table} ({$fields}) VALUES ({$places})";
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
        foreach ($this->data as $index => $place) {
            $this->statement->bindValue(":{$index}", $place, (is_int($place) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
        }
    }
    
    
    // Methods privates
    
    /**
     *
     *
     * @param string $table
     * @param array  $data
     */
    public function execMulti($table, array $data)
    {
        // INSERT INTO table (colunas) VALUES (?), (?), (?), (?)
        $this->table = (string)$table;
        $this->data = (array)$data;
        
        $fields = implode(', ', array_keys($this->data[0]));
        $places = null;
        $inserts = null;
        $links = count(array_keys($this->data[0]));
        
        foreach ($data as $valueMulti) {
            $places .= '(';
            $places .= str_repeat('?, ', $links);
            $places .= '), ';
            
            foreach ($valueMulti as $item) {
                $inserts[] = $item;
            }
        }
        
        $places = rtrim(str_replace(', )', ')', $places), ', ');
        $this->data = $inserts;
        
        try {
            $statement = "INSERT INTO {$this->table} ({$fields}) VALUES {$places}";
            $this->statement = $this->conn->prepare($statement);
            $this->statement->execute($this->data);
            $this->result = $this->conn->lastInsertId();
        } catch (\PDOException $e) {
            $this->result = null;
            die($e->getMessage());
        }
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
