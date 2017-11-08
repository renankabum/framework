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
     * Class DeleteStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class DeleteStatement extends StatementContainer
    {
        /**
         * @param string $table
         * @param string $terms
         * @param mixed  $places
         *
         * @return bool
         */
        public function exec($table, $terms = null, $places = null)
        {
            $this->table = (string) $table;
            $this->terms = (string) $terms;

            // Recupera o places
            $this->setPlaces($places);

            // Executa o bind e query
            $this->execute();

            // Recupera o resultado
            $this->result = $this->stmt->rowCount();

            return $this->result;
        }

        /**
         * @param $places
         *
         * @return bool
         */
        public function execPlaces($places)
        {
            // Recupera o places
            $this->setPlaces($places);

            // Executa o bind e query
            $this->execute();

            // Recupera o resultado
            $this->result = $this->stmt->rowCount();

            return $this->result;
        }

        /**
         * @return bool|int
         */
        public function getResult()
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
            $this->sql = "DELETE FROM {$this->table} {$this->terms}";

            return $this->sql;
        }
    }
}
