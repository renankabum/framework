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

namespace Navegarte\Providers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Navegarte\Contracts\ServiceProviderAbstract;
use Slim\Container;

/**
 * Class LoggerServiceProvider
 *
 * @package Navegarte\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class LoggerServiceProvider extends ServiceProviderAbstract
{
  /**
   * Registers services on the given container.
   *
   * @param \Slim\Container $container
   *
   * @return mixed|void
   */
  public function register(Container $container)
  {
    /**
     * @return \Closure
     */
    $container['logger'] = function () {
      /**
       * @param string|null $file
       *
       * @return \Monolog\Logger
       */
      return function ($file = null) {
        /**
         * Instance logger
         */
        $logger = new Logger('log');
        
        /**
         * Verify name/dir
         */
        if (is_null($file)) {
          $file = 'log.' . substr(md5(date('Ymd')), 0, 10) . '.log';
        } else {
          $file = $file . '.' . substr(md5(date('Ymd')), 0, 10) . '.log';
        }
        
        /**
         * Salved log dir
         */
        $dirOutput = ROOT . "/storage/logs/{$file}";
        
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
