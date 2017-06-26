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

namespace Navegarte\Middleware;

use Navegarte\Contracts\MiddlewareAbstract;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ConfigurationMiddleware
 *
 * @package Navegarte\Middleware
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class ConfigurationMiddleware extends MiddlewareAbstract
{
    /**
     * Register middleware
     *
     * @param \Psr\Http\Message\ServerRequestInterface|\Slim\Http\Request $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response     $response PSR7 response
     * @param callable                                                    $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        /**
         * Before middleware
         */
        
        /**
         * Error app
         */
        set_error_handler(
            function ($code, $message, $file, $line) {
                if (!($code & error_reporting())) {
                    return;
                }
                throw new \ErrorException($message, $code, 0, $file, $line);
            }
        );
        
        /**
         * Locale language
         */
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
        
        /**
         * Init session
         */
        if (session_status() != PHP_SESSION_ACTIVE) {
            $current = session_get_cookie_params();
            
            session_set_cookie_params($current['lifetime'], $current['path'], $current['domain'], $current['secure'], true);
            session_name(md5(md5('VCWeb' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['PHP_SELF'])));
            session_cache_limiter('nocache');
            if (!session_id()) {
                session_start();
            }
        }
        
        /**
         * Configuration timezone app
         */
        date_default_timezone_set(config('app.timezone', 'America/Sao_Paulo'));
        
        /**
         * Configuration default charset app
         */
        ini_set('default_charset', 'UTF-8');
        
        /**
         * Char set mb internal
         */
        mb_internal_encoding('UTF-8');
        
        /**
         * Switch environment app
         */
        switch (config('app.environment', 'production')) {
            case 'development';
                ini_set('error_reporting', -1);
                ini_set('display_errors', 1);
                /*error_reporting(E_ALL);*/
                break;
            
            case 'production':
                ini_set('error_reporting', 0);
                ini_set('display_errors', 0);
                /*error_reporting(E_ERROR);*/
                break;
            
            default:
                header('HTTP/1.1 503 Service Unavailable', true, 503);
                echo 'The application environment is not set correctly.';
                die(1);
                break;
        }
        
        /**
         * After middleware
         */
        $response = $next($request, $response);
        
        return $response;
    }
}
