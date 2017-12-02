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
     * Class TwigProvider
     *
     * @package Core\Providers\View\Engine
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Twig
    {
        /**
         * @var \Twig_Loader_Filesystem
         */
        protected $loader;

        /**
         * @var \Twig_Environment
         */
        protected $environment;

        /**
         * TwigProvider constructor.
         *
         * @param string|array $path
         * @param array        $settings
         */
        public function __construct($path, array $settings = [])
        {
            $this->loader = $this->createLoader(is_string($path) ? [$path] : $path);
            $this->environment = new \Twig_Environment($this->loader, $settings);

            /**
             * Add default extension debug
             */
            $this->addExtension(new \Twig_Extension_Debug());
            $this->addExtension(new TwigExtension());
        }

        /**
         * @param string $template
         * @param array  $data
         *
         * @return string
         */
        public function fetch($template, array $data)
        {
            return $this->environment->render($template, $data);
        }

        /**
         * Render template views
         *
         * @param Response $response
         * @param string   $template
         * @param array    $data
         *
         * @return Response
         */
        public function render(Response $response, $template, array $data = [])
        {
            $response->getBody()
                ->write($this->fetch($template, $data));

            return $response;
        }

        /**
         * Add new extension
         *
         * @param \Twig_ExtensionInterface $extension
         *
         * @return $this
         */
        public function addExtension(\Twig_ExtensionInterface $extension)
        {
            $this->environment->addExtension($extension);

            return $this;
        }

        /**
         * Add new function
         *
         * @param string   $name
         * @param callable $callable
         * @param array    $options
         *
         * @return $this
         */
        public function addFunction($name, $callable, array $options = [])
        {
            $this->environment->addFunction(new \Twig_SimpleFunction($name, $callable, $options));

            return $this;
        }

        /**
         * Add new filter
         *
         * @param string   $name
         * @param callable $callable
         * @param array    $options
         *
         * @return $this
         */
        public function addFilter($name, $callable, array $options = [])
        {
            $this->environment->addFilter(new \Twig_SimpleFilter($name, $callable, $options));

            return $this;
        }

        /**
         * Add new global
         *
         * @param string $name
         * @param mixed  $value
         *
         * @return $this
         */
        public function addGlobal($name, $value)
        {
            $this->environment->addGlobal($name, $value);

            return $this;
        }

        /**
         * Get instanceof TwigProvider
         *
         * @return \Twig_Environment
         */
        public function getEnvironment()
        {
            return $this->environment;
        }

        /**
         * @param array $paths
         *
         * @return \Twig_Loader_Filesystem
         */
        private function createLoader(array $paths)
        {
            $loader = new \Twig_Loader_Filesystem();

            foreach ($paths as $namespace => $path) {
                if (is_string($namespace)) {
                    $loader->setPaths($path, $namespace);
                } else {
                    $loader->addPath($path);
                }
            }

            return $loader;
        }
    }
}