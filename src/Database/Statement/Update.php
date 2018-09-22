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
     * Class Update
     *
     * @package Core\Connect\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Update extends Statement
    {
        /**
         * @var array
         */
        protected $columns = [];
        
        /**
         * @param string $table
         * @param array  $columns
         * @param string $terms
         * @param mixed  $places
         *
         * @return bool
         * @throws \Exception
         */
        public function exec($table, array $columns, $terms = null, $places = null)
        {
            $this->table = (string) $table;
            $this->columns = (array) $columns;
            $this->terms = (string) $terms;
            
            // Recupera o places
            $this->setPlaces($places);
            
            try {
                // Executa o bind e query
                $this->execute();
                
                // Recupera o resultado
                $this->result = $this->stmt->rowCount();
            } catch (\PDOException $e) {
                throw new \Exception("[UPDATE] :: {$e->getMessage()}", (is_int($e->getCode()) ? $e->getCode() : 500));
            }
            
            // Retorna o resultado
            return $this->result;
        }
        
        /**
         * @param mixed $places
         *
         * @return bool
         * @throws \Exception
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
                throw new \Exception("[UPDATE] :: {$e->getMessage()}", (is_int($e->getCode()) ? $e->getCode() : 500));
            }
            
            // Retorna o resultado
            return $this->result;
        }
        
        /**
         * @return int
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
            $columns = [];
            
            foreach ((array) $this->columns as $key => $value) {
                $time = '';
                
                if (!empty($this->places[$key])) {
                    $time = time();
                }
                
                $columns[] = "{$key} = :{$key}{$time}";
                
                $this->places[$key.$time] = (!isset($value)) ? null : $value;
            }
            
            $this->columns = implode(', ', $columns);
            
            $sql = "UPDATE {$this->table} SET {$this->columns} {$this->terms}";
            
            return $sql;
        }
    }
}
