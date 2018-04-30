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

namespace Core\Database\Statement {
    
    use Core\Database\Statement;
    
    /**
     * Class ReadStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class ReadStatement extends Statement
    {
        /**
         * @param string $table
         * @param string $terms
         * @param mixed  $places
         *
         * @return \Core\Database\Statement\ReadStatement
         * @throws \Exception
         */
        public function exec($table, $terms = null, $places = null)
        {
            // Trata os dados
            $this->table = (string)$table;
            $this->terms = (string)$terms;
            
            // Recupera o places
            $this->setPlaces($places);
            
            // Monta a query
            $sql = "SELECT * FROM {$this->table} {$this->terms}";
            
            try {
                // Executa o bind e query
                $this->execute($sql);
            } catch (\PDOException $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
            }
            
            return $this;
        }
        
        /**
         * @param string $sql
         * @param mixed  $places
         *
         * @return \Core\Database\Statement\ReadStatement
         * @throws \Exception
         */
        public function query($sql, $places = null)
        {
            // Trata a query
            $sql = (string)$sql;
            
            // Recupera o places
            $this->setPlaces($places);
            
            try {
                // Executa o bind e query
                $this->execute($sql);
            } catch (\PDOException $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
            }
            
            return $this;
        }
        
        /**
         * @param $places
         *
         * @return \Core\Database\Statement\ReadStatement
         * @throws \Exception
         */
        public function execPlaces($places)
        {
            // Recupera o places
            $this->setPlaces($places);
            
            try {
                // Executa o bind e query
                $this->execute();
            } catch (\PDOException $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
            }
            
            return $this;
        }
        
        /**
         * @return mixed
         */
        public function fetch()
        {
            $this->result = $this->stmt->fetch();
            
            //$this->stmt->closeCursor();
            
            return $this->result;
        }
        
        /**
         * @return array
         */
        public function fetchAll()
        {
            $this->result = $this->stmt->fetchAll();
            
            //$this->stmt->closeCursor();
            
            return $this->result;
        }
        
        /**
         * @return int
         */
        public function rowCount()
        {
            return $this->stmt->rowCount();
        }
        
        /**
         * @return array
         */
        public function getResult()
        {
            return $this->fetchAll();
        }
        
        /**
         * @return int
         */
        public function getRowCount()
        {
            $rowCount = $this->stmt->rowCount();
            
            if ($rowCount === -1) {
                $rowCount = count($this->fetchAll());
            }
            
            return $rowCount;
        }
        
        /**
         * @return string
         */
        public function __toString()
        {
            return '';
        }
    }
}
