<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 08/03/2019 Vagner Cardoso
 */

namespace Core\Providers\Logger {
    
    use Core\Helpers\Helper;
    use Monolog\Formatter\LineFormatter;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger as Monolog;
    
    /**
     * Class Logger
     *
     * @package Core\Providers\Logger
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Logger extends Monolog
    {
        /**
         * @var string
         */
        protected $filename;
        
        /**
         * @var string
         */
        protected $directory;
        
        /**
         * Logger constructor.
         *
         * @param string $name
         * @param string $directory
         */
        public function __construct($name, $directory = null)
        {
            parent::__construct($name);
            $this->filename = 'app';
            $this->directory = $directory;
            $this->initProcessor();
            $this->initHandler();
        }
        
        /**
         * Cria uma nova instância da classe com novo
         * nome do arquivo de salvamento
         *
         * @param string $filename
         *
         * @return $this
         */
        public function filename($filename)
        {
            $new = clone $this;
            $new->filename = (string) $filename;
            $new->initProcessor();
            $new->initHandler();
            
            return $new;
        }
        
        /**
         * Cria o diretório e retorna o caminho
         *
         * @return string
         */
        public function getDirectory()
        {
            // Variávies
            if (empty($this->directory)) {
                $this->directory = APP_FOLDER.'/storage/logs';
            }
            
            // Cria o diretório
            if (!is_dir($this->directory)) {
                mkdir($this->directory, 0755, true);
            }
            
            return sprintf(
                '%s/%s-%s.log',
                $this->directory,
                $this->filename,
                date('Ymd')
            );
        }
        
        /**
         * {@inheritdoc}
         */
        protected function initProcessor()
        {
            $this->pushProcessor(function ($record) {
                $record['extra']['ip'] = $ip = Helper::getIpAddress();
                $record['extra']['hostname'] = gethostbyaddr($ip);
                $record['extra']['useragent'] = Helper::getUserAgent();
                
                return $record;
            });
        }
        
        /**
         * {@inheritdoc}
         */
        protected function initHandler()
        {
            try {
                // StreamHandler
                $stream = new StreamHandler($this->getDirectory(), self::DEBUG);
                $separate = str_repeat('=', 150);
                $formatter = new LineFormatter(
                    "{$separate}\n[%datetime%] %channel%.%level_name%: %message% \n%context% \n%extra%\n{$separate}\n\n"
                );
                $stream->setFormatter($formatter);
                $this->pushHandler($stream);
            } catch (\Exception $e) {
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function __clone()
        {
            $this->processors = [];
            $this->handlers = [];
        }
    }
}
