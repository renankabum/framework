<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
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
             * @return \Core\Providers\Database\Database
             */
            $this->container['db'] = function () {
                return new Database();
            };
        }
    }
}
