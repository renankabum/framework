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
     * Class InsertStatement
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class InsertStatement extends StatementContainer
    {
        /**
         * InsertStatement constructor.
         *
         * @param \Core\Database\Database $dbh
         */
        public function __construct(Database $dbh)
        {
            parent::__construct($dbh);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            $sql = '';

            return $sql;
        }

        /**
         * @param bool $insertId
         *
         * @return string
         */
        public function execute($insertId = true)
        {
            if (!$insertId) {
                return parent::execute();
            }

            parent::execute();

            return $this->dbh->lastInsertId();
        }
    }
}
