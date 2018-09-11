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

namespace Core\Providers\View {
    
    use Psr\Http\Message\ResponseInterface as Response;
    
    /**
     * Class Php
     *
     * @package Core\Providers\View
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Php
    {
        /**
         * @var string
         */
        protected $path;
        
        /**
         * @var string
         */
        protected $pathPhp;
        
        /**
         * @var array
         */
        protected $context = [];
        
        /**
         * @var string
         */
        protected $content;
        
        /**
         * Php constructor.
         *
         * @param string $path
         */
        public function __construct($path)
        {
            $this->path = rtrim($path, '/\\');
            $this->pathPhp = $this->path.'/layout/app.php';
        }
        
        /**
         * Renderiza a view
         *
         * @param Response $response
         * @param string   $template
         * @param array    $context
         *
         * @return Response
         * @throws \Exception
         * @throws \Throwable
         */
        public function render(Response $response, $template, array $context = [])
        {
            try {
                $response->getBody()->write($this->fetch($template, $context));
                
                return $response;
            } catch (\Exception $e) {
                throw $e;
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        
        /**
         * Cria o render
         *
         * @param string $template
         * @param array  $context
         *
         * @return string
         * @throws \Exception
         * @throws \Throwable
         */
        public function fetch($template, array $context = [])
        {
            // Remove extension if passed.
            if (substr($template, -4) === '.php') {
                $template = substr($template, 0, -4);
            }
            
            // Replace `dot` in `bar`
            $template = str_replace('.', '/', $template);
            
            // Verify if exists file
            if (!is_file("{$this->path}/{$template}.php")) {
                throw new \Exception("[VIEW::PHP] O template `{$template}` não existe.");
            }
            
            $this->context = $context;
            
            try {
                ob_start();
                
                extract($this->context);
                
                $this->content = "{$this->path}/{$template}.php";
                
                if (file_exists($this->pathPhp)) {
                    include "{$this->pathPhp}";
                } else {
                    include "{$this->content}";
                }
                
                $output = ob_get_clean();
            } catch (\Exception $e) { // PHP < 7
                ob_end_clean();
                
                throw $e;
            } catch (\Throwable $e) { // PHP 7+
                ob_end_clean();
                
                throw $e;
            }
            
            return $output;
        }
        
        /**
         * Método para incluir todas view dentro
         * do template `app.php`
         */
        public function content()
        {
            include "{$this->content}";
        }
        
        /**
         * Recupera a context, atributos e container da classe
         *
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            if (array_key_exists($name, $this->context)) {
                return $this->context[$name];
            }
        }
    }
}
