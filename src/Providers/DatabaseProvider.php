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

namespace Core\Providers {

    use Core\Contracts\Provider;
    use Core\Database\Database;
    use Core\Database\Statement\CreateStatement;
    use Core\Database\Statement\DeleteStatement;
    use Core\Database\Statement\ReadStatement;
    use Core\Database\Statement\UpdateStatement;

    /**
     * Class DatabaseProvider
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class DatabaseProvider extends Provider
    {
        /**
         * Registers services on the given container.
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return CreateStatement
             */
            $this->container['create'] = function () {
                if (empty($create)) {
                    $create = new CreateStatement;
                }

                return $create;
            };

            /**
             * @return ReadStatement
             */
            $this->container['read'] = function () {
                if (empty($read)) {
                    $read = new ReadStatement;
                }

                return $read;
            };

            /**
             * @return UpdateStatement
             */
            $this->container['update'] = function () {
                if (empty($update)) {
                    $update = new UpdateStatement;
                }

                return $update;
            };

            /**
             * @return DeleteStatement
             */
            $this->container['delete'] = function () {
                if (empty($delete)) {
                    $delete = new DeleteStatement;
                }

                return $delete;
            };

            /**
             * @return \Core\Database\Database|\PDO
             */
            $this->container['db'] = function () {
                if (empty($dbh)) {
                    $dbh = new Database;
                }

                return $dbh;
            };
        }
    }
}