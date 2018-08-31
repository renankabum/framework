<?php

/**
 * VCWeb <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
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
             * @return \Core\Database\Database|\PDO
             * @throws \Exception
             */
            $this->container['db'] = function () {
                if (empty($dbh)) {
                    $dbh = new Database();
                }
                
                return $dbh;
            };
            
            /**
             * @return CreateStatement
             */
            $this->container['create'] = function () {
                if (empty($create)) {
                    $create = new CreateStatement($this->container);
                }
                
                return $create;
            };
            
            /**
             * @return ReadStatement
             */
            $this->container['read'] = function () {
                if (empty($read)) {
                    $read = new ReadStatement($this->container);
                }
                
                return $read;
            };
            
            /**
             * @return UpdateStatement
             */
            $this->container['update'] = function () {
                if (empty($update)) {
                    $update = new UpdateStatement($this->container);
                }
                
                return $update;
            };
            
            /**
             * @return DeleteStatement
             */
            $this->container['delete'] = function () {
                if (empty($delete)) {
                    $delete = new DeleteStatement($this->container);
                }
                
                return $delete;
            };
        }
    }
}
