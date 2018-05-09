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
     * Class CreateStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class CreateStatement extends Statement
    {
        /**
         * @param string $table
         * @param array  $columns
         *
         * @return string|int
         * @throws \Exception
         */
        public function exec($table, array $columns)
        {
            // Trata os dados
            $this->table = (string)$table;
            $this->places = (array)$columns;
            
            try {
                // Executa o bind e query
                $this->execute();
                
                // Recupera o resultado
                $this->result = $this->container['db']->lastInsertId();
            } catch (\PDOException $e) {
                $code = $e->getCode();
                
                if (is_string($code)) {
                    $code = 500;
                }
                
                throw new \Exception($e->getMessage(), $code);
            }
            
            // Retorna os resultado
            return $this->result;
        }
        
        /**
         * @param string $table
         * @param array  $columns
         *
         * @return int
         * @throws \Exception
         */
        public function execMultiple($table, array $columns)
        {
            $this->table = (string)$table;
            $this->places = (array)$columns;
            
            // Verifica se o places está no formato correto
            if (empty($this->places[0])) {
                throw new \Exception("Para usar esse método e preciso passar um array multidimensional com os dados para inserir no banco.", E_USER_WARNING);
            }
            
            // Monta o binds e query
            $fields = implode(', ', array_keys($this->places[0]));
            $values = [];
            $places = [];
            
            $i = 0;
            foreach ($this->places as $place) {
                $i++;
                
                $values[] = ":".implode("{$i}, :", array_keys($place)).$i;
                
                foreach ($place as $k => $v) {
                    $places[$k.$i] = $v;
                }
            }
            
            $values = '('.implode('), (', $values).')';
            $this->places = $places;
            $sql = "INSERT INTO {$this->table} ({$fields}) VALUES {$values}";
            //
            
            try {
                // Prepara a query
                $this->stmt = $this->container['db']->prepare($sql);
                
                // Binds values
                if (is_array($this->places) && !empty($this->places)) {
                    $this->setBinds($this->places);
                }
                
                // Executa a query
                $this->stmt->execute();
                
                // Recupera o resultado
                $this->result = $this->stmt->rowCount();
            } catch (\PDOException $e) {
                $code = $e->getCode();
                
                if (is_string($code)) {
                    $code = 500;
                }
                
                throw new \Exception($e->getMessage(), $code);
            }
            
            // Retorna o resultado
            return $this->result;
        }
        
        /**
         * @return bool|boolean
         */
        public function lastInsertId()
        {
            if ($this->result === 0) {
                return false;
            }
            
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
         * @return string
         */
        public function __toString()
        {
            $fields = implode(', ', array_keys($this->places));
            $values = ":".implode(", :", array_keys($this->places));
            
            $sql = "INSERT INTO {$this->table} ($fields) VALUES ({$values})";
            
            return $sql;
        }
    }
}
