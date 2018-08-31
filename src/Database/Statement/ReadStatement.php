<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
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
         * @return ReadStatement
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
                $code = $e->getCode();
                
                if (is_string($code)) {
                    $code = 500;
                }
                
                throw new \Exception($e->getMessage(), $code);
            }
            
            return $this;
        }
        
        /**
         * @param string $sql
         * @param mixed  $places
         *
         * @return ReadStatement
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
                $code = $e->getCode();
                
                if (is_string($code)) {
                    $code = 500;
                }
                
                throw new \Exception($e->getMessage(), $code);
            }
            
            return $this;
        }
        
        /**
         * @param $places
         *
         * @return ReadStatement
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
                $code = $e->getCode();
                
                if (is_string($code)) {
                    $code = 500;
                }
                
                throw new \Exception($e->getMessage(), $code);
            }
            
            return $this;
        }
        
        /**
         * @return mixed
         */
        public function fetch()
        {
            $this->result = $this->stmt->fetch();
            
            return $this->result;
        }
        
        /**
         * @return array
         */
        public function fetchAll()
        {
            $this->result = $this->stmt->fetchAll();
            
            return $this->result;
        }
        
        /**
         * @return int
         */
        public function rowCount()
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
