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
    
    use Core\Database\DatabaseException;
    use Core\Database\Statement;
    
    /**
     * Class DeleteStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class DeleteStatement extends Statement
    {
        /**
         * @param string $table
         * @param string $terms
         * @param mixed  $places
         *
         * @return bool
         * @throws \Core\Database\DatabaseException
         */
        public function exec($table, $terms = null, $places = null)
        {
            $this->table = (string)$table;
            $this->terms = (string)$terms;
            
            // Recupera o places
            $this->setPlaces($places);
            
            try {
                // Executa o bind e query
                $this->execute();
                
                // Recupera o resultado
                $this->result = $this->stmt->rowCount();
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage(), $e->getCode());
            }
            
            // Retorna o resultado
            return $this->result;
        }
        
        /**
         * @param $places
         *
         * @return bool
         * @throws \Core\Database\DatabaseException
         */
        public function execPlaces($places)
        {
            // Recupera o places
            $this->setPlaces($places);
            
            try {
                // Executa o bind e query
                $this->execute();
                
                // Recupera o resultado
                $this->result = $this->stmt->rowCount();
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage(), $e->getCode());
            }
            
            // Retorna o resultado
            return $this->result;
        }
        
        /**
         * @return int|boolean
         */
        public function rowCount()
        {
            if ($this->result === 0) {
                return false;
            }
            
            return $this->result;
        }
        
        /**
         * @return string
         */
        public function __toString()
        {
            $sql = "DELETE FROM {$this->table} {$this->terms}";
            
            return $sql;
        }
    }
}
