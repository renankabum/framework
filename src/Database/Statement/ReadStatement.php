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

    /**
     * Class ReadStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class ReadStatement extends StatementContainer
    {
        /**
         * @param string $table
         * @param string $terms
         * @param mixed  $places
         *
         * @return \Core\Database\Statement\ReadStatement
         */
        public function exec($table, $terms = null, $places = null)
        {
            $this->table = (string) $table;
            $this->terms = (string) $terms;

            // Recupera o places
            $this->setPlaces($places);

            // Monta a query
            $this->sql = "SELECT * FROM {$this->table} {$this->terms}";

            // Executa o bind e query
            $this->execute();

            // Recupera o resultado
            $this->result = true;

            return $this;
        }

        /**
         * @param string $sql
         * @param mixed  $places
         *
         * @return \Core\Database\Statement\ReadStatement
         */
        public function query($sql, $places = null)
        {
            $this->sql = $sql;

            // Recupera o places
            $this->setPlaces($places);

            // Executa o bind e query
            $this->execute();

            // Recupera o resultado
            $this->result = true;

            return $this;
        }

        /**
         * @param $places
         *
         * @return \Core\Database\Statement\ReadStatement
         */
        public function execPlaces($places)
        {
            // Recupera o places
            $this->setPlaces($places);

            // Executa o bind e query
            $this->execute();

            // Recupera o resultado
            $this->result = true;

            return $this;
        }

        /**
         * @return array
         */
        public function fetchAll()
        {
            $this->result = $this->stmt->fetchAll();
            $this->stmt->closeCursor();

            return $this->result;
        }

        /**
         * @return mixed
         */
        public function fetch()
        {
            $this->result = $this->stmt->fetch();
            $this->stmt->closeCursor();

            return $this->result;
        }

        /**
         * @return int
         */
        public function rowCount()
        {
            if ($this->stmt->rowCount() === -1) {
                return count($this->fetchAll());
            }

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
            return $this->rowCount();
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
