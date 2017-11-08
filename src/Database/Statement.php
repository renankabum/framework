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

namespace Core\Database {

    /**
     * Class Statement
     *
     * @package Core\Database
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Statement extends \PDOStatement
    {
        /**
         * @var \Core\Database\Database|\PDO
         */
        protected $dbh;

        /**
         * Statement constructor.
         *
         * @param \Core\Database\Database|\PDO $dbh
         */
        protected function __construct(Database $dbh)
        {
            $this->dbh = $dbh;
        }
    }
}
