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
    use Core\Database\Connect;
    use Core\Database\Statement\Create;
    use Core\Database\Statement\Delete;
    use Core\Database\Statement\Read;
    use Core\Database\Statement\Update;
    
    /**
     * Class DatabaseProvider
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class DatabaseProvider extends Provider
    {
        /**
         * Registra os serviços para trabalhar com banco de dados
         *
         * @return void
         * @throws \Exception
         */
        public function register()
        {
            // Coneção
            $this->container['db'] = function () {
                return new Connect();
            };
            
            // Criação
            $this->container['create'] = function () {
                return new Create();
            };
            
            // Leitura
            $this->container['read'] = function () {
                return new Read();
            };
            
            // Atualização
            $this->container['update'] = function () {
                return new Update();
            };
            
            // Remover
            $this->container['delete'] = function () {
                return new Delete();
            };
        }
    }
}
