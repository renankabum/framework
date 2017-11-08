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
     * Class Read
     *
     * @package Core\Database\Old
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Read
    {
        /**
         * @var string
         */
        private $select;

        /**
         * @var array
         */
        private $places;

        /**
         * @var array
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
            $this->conn = new Database;
        }

        /**
         * Executa a query simplificada
         *
         * @param string $table
         * @param string $terms
         * @param string $places
         *
         * @return $this
         */
        public function exec($table, $terms = null, $places = null)
        {
            if (!empty($places)) {
                parse_str($places, $this->places);
            }

            $this->select = "SELECT * FROM {$table} {$terms}";

            $this->execute();

            return $this;
        }

        /**
         * Executa a query passando toda ela
         *
         * @param string $query
         * @param string $places
         *
         * @return $this
         */
        public function query($query, $places = null)
        {
            $this->select = (string) $query;

            if (!empty($places)) {
                parse_str($places, $this->places);
            }

            $this->execute();

            return $this;
        }

        /**
         * Muda os place da query para uma nova consulta
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
         * Retorna um array com todos os dados obtidos na consulta
         *
         * @return array
         */
        public function getResult()
        {
            return $this->result;
        }

        /**
         * Pega o primeiro resultado
         *
         * @return array|mixed
         */
        public function first()
        {
            $array = [];

            if (!empty($this->result)) {
                $array = $this->result[0];
            }

            return $array;
        }

        /**
         * Obtém o número de registro encontrados
         *
         * @return int
         */
        public function getRowCount()
        {
            if ($this->statement->rowCount() == -1) {
                return count($this->getResult());
            }

            return $this->statement->rowCount();
        }

        /**
         * Obtém o PDO e Prepara a Query
         */
        private function connect()
        {
            $this->statement = $this->conn->prepare($this->select);
        }

        /**
         * Cria a syntax da query para prepared statement
         */
        private function syntax()
        {
            if ($this->places) {
                Connect::bindValues($this->statement, $this->places);
            }
        }

        /**
         * Obtém a conexão a syntax e executa a query
         */
        private function execute()
        {
            try {
                $this->conn->beginTransaction();
                $this->connect();
                $this->syntax();
                $this->statement->execute();
                $this->result = $this->statement->fetchAll();
                $this->statement->closeCursor();
                $this->conn->commit();
            } catch (\PDOException $e) {
                $this->result = null;
                $this->conn->rollBack();

                throw new \Exception("Read:: {$e->getMessage()}");
            }
        }
    }
}
