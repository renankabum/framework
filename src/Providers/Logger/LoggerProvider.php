<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 08/03/2019 Vagner Cardoso
 */

namespace Core\Providers\Logger {
    
    use Core\Contracts\Provider;
    
    /**
     * Class LoggerProvider
     *
     * @package Core\Providers\Logger
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class LoggerProvider extends Provider
    {
        /**
         * Registra serviço para gerar logs (Monolog)
         *
         * @return void
         */
        public function register()
        {
            $this->container['logger'] = function () {
                return new Logger(
                    'VCWEBNETWORKS', APP_FOLDER.'/storage/logs'
                );
            };
        }
    }
}
