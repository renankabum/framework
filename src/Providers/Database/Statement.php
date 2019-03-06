<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 05/03/2019 Vagner Cardoso
 */

namespace Core\Providers\Database {
    
    /**
     * Class Statement
     *
     * @package Core\Providers\Database
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Statement extends \PDOStatement
    {
        /**
         * @var Database
         */
        protected $db;
        
        /**
         * Statement constructor.
         *
         * @param Database $db
         */
        protected function __construct(Database $db)
        {
            $this->db = $db;
        }
        
        /**
         * @return \Core\Providers\Database\Database
         */
        public function getPdo()
        {
            return $this->db;
        }
        
        /**
         * @param string $name
         *
         * @return string
         */
        public function lastInsertId($name = null)
        {
            return $this->db->lastInsertId($name);
        }
        
        /**
         * @return int
         */
        public function rowCount()
        {
            $rowCount = parent::rowCount();
            
            if ($rowCount === -1) {
                $rowCount = count($this->fetchAll());
            }
            
            return $rowCount;
        }
        
        /**
         * @param int $fetchStyle
         * @param mixed $fetchArgument
         * @param array $ctorArgs
         *
         * @return array
         */
        public function fetchAll($fetchStyle = null, $fetchArgument = null, $ctorArgs = null)
        {
            if (is_null($fetchStyle)) {
                $fetchStyle = \PDO::FETCH_ASSOC;
            }
            
            if ($fetchStyle === \PDO::FETCH_BOTH) {
                return parent::fetchAll();
            } else if ($fetchStyle === \PDO::FETCH_CLASS) {
                return parent::fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
            } else if (in_array($fetchStyle, [\PDO::FETCH_ASSOC, \PDO::FETCH_NUM, \PDO::FETCH_OBJ])) {
                return parent::fetchAll($fetchStyle);
            } else {
                return parent::fetchAll($fetchStyle, $fetchArgument);
            }
        }
    }
}
