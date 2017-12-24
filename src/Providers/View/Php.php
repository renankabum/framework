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

namespace Core\Providers\View {

    use Psr\Http\Message\ResponseInterface as Response;

    /**
     * Class Php
     *
     * @package Core\Providers\View
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Php
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
            $this->pathPhp = $this->path . '/app.php';
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
            if (!is_file($this->path . '/' . $template)) {
                throw new \Exception("O template `{$template}` não existe.");
            }

            $this->context = $context;

            try {
                ob_start();

                extract($this->context);

                $this->content = "{$this->path}/{$template}";

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
                $output = $this->fetch($template, $context);

                $response->getBody()
                    ->write($output);
            } catch (\Exception $e) {
                throw $e;
            } catch (\Throwable $e) {
                throw $e;
            }

            return $response;
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
