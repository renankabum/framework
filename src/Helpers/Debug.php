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

namespace Core\Helpers {
    
    use Symfony\Component\VarDumper\Cloner\VarCloner;
    use Symfony\Component\VarDumper\Dumper\CliDumper;
    use Symfony\Component\VarDumper\Dumper\HtmlDumper;
    
    /**
     * Class DumpHelper
     *
     * @package Core\Helpers
     */
    class Debug
    {
        /**
         * Melhoria no dumper
         *
         * @param mixed $var
         */
        public function dump($var)
        {
            if (class_exists(CliDumper::class)) {
                $dump = is_php_cli() ? new CliDumper() : new HtmlDumper();
                $dump->dump((new VarCloner())->cloneVar($var));
            } else {
                var_dump($var);
            }
        }
    }
}
