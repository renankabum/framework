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
     * Class UpdateStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class UpdateStatement extends StatementContainer
    {
        /**
         * @var array
         */
        protected $columns = [];

        /**
         * @param string $table
         * @param array  $columnsOrPairs
         * @param string $terms
         * @param mixed  $places
         *
         * @return bool
         */
        public function exec($table, array $columnsOrPairs, $terms = null, $places = null)
        {
            $this->table = (string) $table;
            $this->columns = (array) $columnsOrPairs;
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
         * @return bool
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
            $columns = [];
            foreach ((array) $this->columns as $key => $value) {
                $time = '';
                if (!empty($this->places[$key])) {
                    $time = time();
                }

                $this->places[$key . $time] = ($value == '' ? null : $value);

                $columns[] = "{$key} = :{$key}{$time}";
            }

            $this->columns = implode(', ', $columns);
            $this->sql = "UPDATE {$this->table} SET {$this->columns} {$this->terms}";

            return $this->sql;
        }
    }
}
