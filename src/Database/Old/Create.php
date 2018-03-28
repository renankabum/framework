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

namespace Core\Database\Old {
    
    use Core\Database\Database;
    
    /**
     * Class Create
     *
     * @package Core\Database\Old
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
         * @var int
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
            $this->conn = new Database();
        }
        
        /**
         * Cria o registro de forma simples
         *
         * @param string $table
         * @param array  $data
         *
         * @return $this
         */
        public function exec($table, array $data)
        {
            $this->table = (string)$table;
            $this->data = (array)$data;
            
            $this->execute();
            
            return $this;
        }
        
        /**
         * Cria vários registro passando um array multimensional
         *
         * @param string $table
         * @param array  $data
         *
         * @throws \Exception
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
            
            foreach ($data as $multi) {
                $places .= '(';
                $places .= str_repeat('?, ', $links);
                $places .= '), ';
                
                foreach ($multi as $item) {
                    $inserts[] = $item;
                }
            }
            
            $places = rtrim(str_replace(', )', ')', $places), ', ');
            $this->data = $inserts;
            
            try {
                $this->conn->beginTransaction();
                
                $statement = "INSERT INTO {$this->table} ({$fields}) VALUES {$places}";
                $this->statement = $this->conn->prepare($statement);
                
                Connect::bindValues($this->statement, $this->data);
                
                $this->statement->execute();
                $this->result = $this->conn->lastInsertId();
                
                $this->conn->commit();
            } catch (\PDOException $e) {
                $this->conn->rollBack();
                
                $this->result = null;
                
                throw new \Exception("Create Multi:: {$e->getMessage()}");
            }
        }
        
        /**
         * Returna o ID do registro inserido
         *
         * @return int
         */
        public function getResult()
        {
            return $this->result;
        }
        
        /**
         * Obtém a quantidade de linhas afetadas
         * ou o ultimo ID inserido
         *
         * @return int
         */
        public function getRowCount()
        {
            if ($this->statement->rowCount() == -1) {
                return $this->result;
            }
            
            return $this->statement->rowCount();
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
            Connect::bindValues($this->statement, $this->data);
        }
        
        /**
         * Cria a syntax da query para prepared statement
         */
        private function syntax()
        {
            $fields = implode(', ', array_keys($this->data));
            $places = ':'.implode(', :', array_keys($this->data));
            
            $this->statement = "INSERT INTO {$this->table} ({$fields}) VALUES ({$places})";
        }
        
        /**
         * Obtém a conexão a syntax e executa a query
         */
        private function execute()
        {
            try {
                $this->conn->beginTransaction();
                $this->syntax();
                $this->connect();
                $this->statement->execute();
                $this->result = $this->conn->lastInsertId();
                $this->statement->closeCursor();
                $this->conn->commit();
            } catch (\PDOException $e) {
                $this->result = null;
                $this->conn->rollBack();
                
                throw new \Exception("Create:: {$e->getMessage()}");
            }
        }
    }
}
