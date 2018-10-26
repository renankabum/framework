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

namespace Core\Database {
    
    use Core\App;
    
    /**
     * Class StatementContainer
     *
     * @property \Core\Database\Connect db
     *
     * @package Core\Connect
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Statement
    {
        /**
         * @var string
         */
        protected $table;
        
        /**
         * @var string
         */
        protected $terms;
        
        /**
         * @var mixed
         */
        protected $result;
        
        /**
         * @var array
         */
        protected $places = [];
        
        /**
         * @var \PDOStatement
         */
        protected $stmt;
        
        /**
         * @return mixed
         */
        abstract public function __toString();
        
        /**
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            return App::getInstance()->resolve($name);
        }
        
        /**
         * @param $places
         */
        protected function setPlaces($places)
        {
            if (!is_array($places)) {
                if (function_exists('mb_parse_str')) {
                    mb_parse_str($places, $this->places);
                } else {
                    parse_str($places, $this->places);
                }
            } else {
                $this->places = (array) $places;
            }
        }
        
        /**
         * Executa o bind e query
         *
         * @param string $sql
         *
         * @throws \Exception
         */
        protected function execute($sql = null)
        {
            try {
                // Prepara a query
                $this->stmt = $this->db->prepare($sql ?: $this);
                
                // Binds values
                if (is_array($this->places) && !empty($this->places)) {
                    $this->setBinds($this->places);
                }
                
                // Executa a query
                $this->stmt->execute();
            } catch (\PDOException $e) {
                throw new \Exception("[STATEMENT] :: {$e->getMessage()}", (is_int($e->getCode()) ? $e->getCode() : 500));
            }
        }
        
        /**
         * @param array $binds
         */
        protected function setBinds($binds)
        {
            foreach ((array) $binds as $key => $bind) {
                if ($key == 'limit' || $key == 'offset') {
                    $bind = (int) $bind;
                }
                
                $this->stmt->bindValue(is_string($key) ? ":{$key}" : ((int) $key + 1), $bind, is_int($bind) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            }
        }
    }
}
