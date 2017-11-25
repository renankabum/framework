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

    use Core\Database\Database;

    /**
     * Class StatementContainer
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class StatementContainer
    {
        /**
         * @var \Core\Database\Database
         */
        protected $dbh;

        /**
         * @var string
         */
        protected $sql;

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
         * @var \Core\Database\Statement
         */
        protected $stmt;

        /**
         * StatementContainer constructor.
         */
        public function __construct()
        {
            $this->dbh = new Database;
        }

        /**
         * @return string
         */
        public function compile()
        {
            return $this->stmt->queryString;
        }

        /**
         * @return bool
         */
        public function beginTransaction()
        {
            return $this->dbh->beginTransaction();
        }

        /**
         * @return bool
         */
        public function commit()
        {
            return $this->dbh->commit();
        }

        /**
         * @return bool
         */
        public function rollBack()
        {
            return $this->dbh->rollBack();
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
         * @param array $binds
         */
        protected function setBinds($binds)
        {
            foreach ((array) $binds as $key => $bind) {
                if ($key == 'limit' || $key == 'offset') {
                    $bind = (int) $bind;
                }

                $this->stmt->bindValue(
                    is_string($key) ? ":{$key}" : (int) $key + 1, $bind, is_int($bind) ? \PDO::PARAM_INT : \PDO::PARAM_STR
                );
            }
        }

        /**
         * Executa o bind e query
         */
        protected function execute()
        {
            try {
                if (!$this instanceof ReadStatement) {
                    $this->sql = $this;
                }

                // Prepara a query
                $this->stmt = $this->dbh->prepare($this->sql);

                // Binds values
                if (is_array($this->places) && !empty($this->places)) {
                    $this->setBinds($this->places);
                }

                // Executa a query
                $this->stmt->execute();
            } catch (\PDOException $e) {
                throw new \Exception($e->getMessage());
            }
        }

        /**
         * @return mixed
         */
        abstract public function __toString();
    }
}
