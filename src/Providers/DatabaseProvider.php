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
            $this->container['db'] = new Connect();
            
            // Criação
            $this->container['create'] = new Create();
            
            // Leitura
            $this->container['read'] = new Read();
            
            // Atualização
            $this->container['update'] = new Update();
            
            // Remover
            $this->container['delete'] = new Delete();
        }
    }
}
