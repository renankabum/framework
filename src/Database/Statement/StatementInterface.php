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
     * Interface StatementInterface
     *
     * @package Core\Database\Statement
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    interface StatementInterface
    {
        /**
         * StatementInterface constructor.
         *
         * @param \Core\Database\Database $dbh
         */
        public function __construct(Database $dbh);

        /**
         * @return mixed
         */
        public function __toString();
    }
}
