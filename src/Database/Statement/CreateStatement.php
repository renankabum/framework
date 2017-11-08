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
     * Class CreateStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class CreateStatement extends StatementContainer
    {
        /**
         * @param string $table
         * @param array  $columnsOrPairs
         *
         * @return string|int
         */
        public function exec($table, array $columnsOrPairs)
        {
            $this->table = (string) $table;
            $this->places = (array) $columnsOrPairs;

            // Executa o bind e query
            $this->execute();

            // Recupera o resultado
            $this->result = $this->dbh->lastInsertId();

            return $this->result;
        }

        /**
         * @param string $table
         * @param array  $columnsOrPairs
         *
         * @return int
         * @throws \Exception
         */
        public function execMultiple($table, array $columnsOrPairs)
        {
            $this->table = (string) $table;
            $this->places = (array) $columnsOrPairs;

            // Verifica se o places está no formato correto
            if (empty($this->places[0])) {
                throw new \Exception("Para usar esse método e preciso passar um array multidimensional com os dados.");
            }

            // Monta o binds e query
            $fields = implode(', ', array_keys($this->places[0]));
            $values = [];
            $places = [];

            $i = 0;
            foreach ($this->places as $place) {
                $i++;

                $values[] = ":" . implode("{$i}, :", array_keys($place)) . $i;

                foreach ($place as $k => $v) {
                    $places[$k . $i] = $v;
                }
            }

            $values = '(' . implode('), (', $values) . ')';
            $this->places = $places;
            $this->sql = "INSERT INTO {$this->table} ({$fields}) VALUES {$values}";
            //

            try {
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

            $this->result = $this->stmt->rowCount();

            return $this->result;
        }

        /**
         * @return int|bool
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
            $fields = implode(', ', array_keys($this->places));
            $values = ":" . implode(", :", array_keys($this->places));

            $this->sql = "INSERT INTO {$this->table} ($fields) VALUES ({$values})";

            return $this->sql;
        }
    }
}
