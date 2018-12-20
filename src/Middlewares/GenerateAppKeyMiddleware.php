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

namespace Core\Middlewares {
    
    use Core\Contracts\Middleware;
    use Core\Helpers\Str;
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class GenerateAppKeyMiddleware
     *
     * @package Core\Middlewares
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class GenerateAppKeyMiddleware extends Middleware
    {
        /**
         * Registra middleware para criação do APP_KEy
         *
         * @param \Slim\Http\Request  $request  PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable            $next     Next middleware
         *
         * @return \Slim\Http\Response
         */
        public function __invoke(Request $request, Response $response, callable $next)
        {
            $originalAppKey = env('APP_KEY', null);
            
            if (empty($originalAppKey)) {
                $newAppKey = 'base64:'.Str::randomBytes(64);
                $scaped = preg_quote("={$originalAppKey}", '/');
                
                file_put_contents(
                    APP_FOLDER.'/.env',
                    preg_replace(
                        "/^APP_KEY{$scaped}/m",
                        "APP_KEY={$newAppKey}",
                        file_get_contents(APP_FOLDER.'/.env')
                    )
                );
            }
            
            $response = $next($request, $response);
            
            return $response;
        }
    }
}
