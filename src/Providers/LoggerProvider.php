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
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    
    /**
     * Class LoggerProvider
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class LoggerProvider extends Provider
    {
        /**
         * Registers services on the given container.
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
                    $logger = new Logger('VCWEB_LOG');
                    
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
                    $logger->pushHandler((new StreamHandler($dirOutput, Logger::DEBUG))->setFormatter($formatter));
                    
                    return $logger;
                };
            };
        }
    }
}
