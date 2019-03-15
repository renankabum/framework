<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 14/03/2019 Vagner Cardoso
 */

namespace Core\Providers\Database {
    
    use Core\Contracts\Provider;
    
    /**
     * Class DatabaseProvider
     *
     * @package Core\Providers\Database
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class DatabaseProvider extends Provider
    {
        /**
         * Registra serviço para trabalhar com banco de dados
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return Database
             */
            $this->container['db'] = function () {
                return Database::getInstance();
            };
        }
    }
}
