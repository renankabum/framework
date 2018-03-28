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
     * Class Update
     *
     * @package Core\Database\Old
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
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
         * @var bool
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
            $this->conn = new Database();
        }
        
        /**
         * Executa o update no banco de dados simplificado
         *
         * @param string      $table
         * @param array       $data
         * @param string      $terms
         * @param string|null $places
         *
         * @return $this;
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
            
            return $this;
        }
        
        /**
         * Muda os place da query para uma nova query
         *
         * @param string $places
         *
         * @return $this
         */
        public function setPlaces($places)
        {
            parse_str($places, $this->places);
            
            $this->execute();
            
            return $this;
        }
        
        /**
         * Retorna true se não ocorrer erros, ou false.
         *
         * @return bool
         */
        public function getResult()
        {
            return $this->result;
        }
        
        /**
         * Retorna o número de linhas alteradas no banco
         * ou retorna true
         *
         * @return int|bool
         */
        public function getRowCount()
        {
            if ($this->statement->rowCount() == -1) {
                return $this->getResult();
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
             * Verifica se os dados passados estão NULL e se estiver seta como vázio
             */
            foreach ($this->data as $index => $place) {
                $this->data[$index] = ($place == '' ? null : $place);
            }
            
            /**
             * Percore os dados e places para montar os binds
             */
            $bindings = $this->data;
            
            if ($this->places) {
                $bindings = array_merge($this->data, $this->places);
            }
            
            Connect::bindValues($this->statement, $bindings);
        }
        
        /**
         * Cria a syntax da query para prepared statement
         */
        private function syntax()
        {
            $places = [];
            foreach ($this->data as $index => $value) {
                $places[] = "{$index} = :{$index}";
            }
            
            $places = implode(', ', $places);
            $this->statement = "UPDATE {$this->table} SET {$places} {$this->terms}";
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
                $this->result = true;
                $this->statement->closeCursor();
                $this->conn->commit();
            } catch (\PDOException $e) {
                $this->result = null;
                $this->conn->rollBack();
                
                throw new \Exception("Update:: {$e->getMessage()}");
            }
        }
    }
}
