<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 28/04/2017 Vagner Cardoso
 */

namespace Core\Providers {
    
    use Core\Contracts\Provider;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger as MonoLogger;
    
    /**
     * Class LoggerProvider
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class LoggerProvider extends Provider
    {
        /**
         * Registra o serviço de logs [Monolog]
         *
         * @return void
         */
        public function register()
        {
            /**
             * @return \Closure
             */
            $this->container['logger'] = function () {
                /**
                 * @param string $file
                 *
                 * @return \Monolog\Logger
                 * @throws \Exception
                 */
                return function ($file = null) {
                    /**
                     * Instance logger
                     */
                    $logger = new MonoLogger('VCWEB_LOG');
                    
                    /**
                     * Verify name/dir
                     */
                    if (is_null($file)) {
                        $file = 'app-'.substr(md5(date('Ymd')), 0, 10).'.log';
                    } else {
                        $file = $file.'-'.substr(md5(date('Ymd')), 0, 10).'.log';
                    }
                    
                    /**
                     * Salved log dir
                     */
                    $dirName = APP_FOLDER."/storage/logs";
                    if (!is_dir($dirName)) {
                        mkdir($dirName, 0755, true);
                    }
                    
                    $dirOutput = "{$dirName}/{$file}";
                    
                    /**
                     * Custom formatter logger
                     */
                    $dateFormat = 'd/m/Y H:i:s';
                    $format = "[%datetime%] %level_name%: \r\n%message% \n%context%\r\n\n";
                    $formatter = new LineFormatter($format, $dateFormat);
                    
                    /**
                     * Create logger handler
                     */
                    $logger->pushHandler((new StreamHandler($dirOutput, MonoLogger::DEBUG))->setFormatter($formatter));
                    
                    return $logger;
                };
            };
        }
    }
}
