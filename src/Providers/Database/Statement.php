<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 14/03/2019 Vagner Cardoso
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
         * @return Database
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
         * @param mixed $fetchStyle
         * @param int $cursorOrientation
         * @param int $cursorOffset
         *
         * @return array|object
         */
        public function fetch($fetchStyle = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
        {
            if ($this->db->isFetchObject() && $fetchStyle !== \PDO::FETCH_ASSOC) {
                return parent::fetchObject(
                    !empty($fetchStyle) ? $fetchStyle : 'stdClass'
                );
            }
            
            return parent::fetch(
                $fetchStyle, $cursorOrientation, $cursorOffset
            );
        }
        
        /**
         * @param int $fetchStyle
         * @param mixed $fetchArgument
         * @param array $ctorArgs
         *
         * @return array|object
         */
        public function fetchAll($fetchStyle = null, $fetchArgument = null, $ctorArgs = null)
        {
            if (empty($fetchStyle)) {
                $fetchStyle = $this->db->getAttribute(
                    \PDO::ATTR_DEFAULT_FETCH_MODE
                );
            }
            
            if ($fetchStyle === \PDO::FETCH_CLASS && empty($fetchArgument)) {
                $fetchArgument = 'stdClass';
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
