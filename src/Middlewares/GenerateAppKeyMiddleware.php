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
         * @param \Slim\Http\Request $request PSR7 request
         * @param \Slim\Http\Response $response PSR7 response
         * @param callable $next Next middleware
         *
         * @return \Slim\Http\Response
         */
        public function __invoke(Request $request, Response $response, callable $next)
        {
            // Gera chaves secretas da aplicação caso não existem
            $this->generateAppKey();
            $this->generateDeployKey();
            $this->generateApiBasicKey();
            
            // Retorna a resposta
            $response = $next($request, $response);
            
            return $response;
        }
        
        /**
         * Gera a chave da aplicação
         */
        protected function generateAppKey()
        {
            $originalKey = env('APP_KEY', null);
            
            if (empty($originalKey)) {
                $key = 'base64:'.Str::randomBytes(64);
                $scaped = preg_quote("={$originalKey}", '/');
                
                file_put_contents(
                    APP_FOLDER.'/.env',
                    preg_replace(
                        "/^APP_KEY{$scaped}/m",
                        "APP_KEY={$key}",
                        file_get_contents(APP_FOLDER.'/.env')
                    )
                );
            }
        }
        
        /**
         * Gera a chave para o deploy
         */
        protected function generateDeployKey()
        {
            $originalKey = env('DEPLOY_TOKEN', null);
            
            if (empty($originalKey)) {
                $key = Str::randomBytes(64);
                $scaped = preg_quote("={$originalKey}", '/');
                
                file_put_contents(
                    APP_FOLDER.'/.env',
                    preg_replace(
                        "/^DEPLOY_TOKEN{$scaped}/m",
                        "DEPLOY_TOKEN={$key}",
                        file_get_contents(APP_FOLDER.'/.env')
                    )
                );
            }
        }
        
        /**
         * Gera a chave para a api
         */
        protected function generateApiBasicKey()
        {
            $originalKey = env('API_TOKEN', null);
            
            if (empty($originalKey)) {
                $key = Str::randomBytes(64);
                $scaped = preg_quote("={$originalKey}", '/');
                
                file_put_contents(
                    APP_FOLDER.'/.env',
                    preg_replace(
                        "/^API_TOKEN{$scaped}/m",
                        "API_TOKEN={$key}",
                        file_get_contents(APP_FOLDER.'/.env')
                    )
                );
            }
        }
    }
}
