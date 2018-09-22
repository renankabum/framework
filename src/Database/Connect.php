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

namespace Core\Database {
    
    use Core\Helpers\Obj;
    
    /**
     * Class Connect
     *
     * @package Core\Connect
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Connect extends \PDO
    {
        /**
         * @var bool
         */
        protected $failed;
        
        /**
         * Connect constructor.
         *
         * @param string $driver
         * @param array  $options
         *
         * @throws \Exception
         */
        public function __construct($driver = null, array $options = [])
        {
            // Carrega as configurações
            $database = Obj::set(config('database'));
            
            if (empty($driver)) {
                $driver = $database->default;
            }
            
            // Pega a configuração do driver escolhido
            $database = $database->connections->{$driver};
            
            try {
                // Pega o dns e os options padrões
                $dsn = sprintf($this->getDns($driver), $database->host, $database->database);
                $options = $this->getDefaultOptions() + $options;
                
                // Realiza a conexão
                parent::__construct($dsn, $database->username, $database->password, $options);
                
                // Usa o banco configurado
                if (isset($database->database)) {
                    $this->exec("USE {$database->database};");
                }
                
                // Configura o encoding
                if (isset($database->charset) && isset($database->collation)) {
                    $this->exec("SET NAMES {$database->charset} COLLATE {$database->collation}");
                }
            } catch (\PDOException $e) {
                $this->failed = true;
                
                throw new \Exception("[CONNECT] :: {$e->getMessage()}", (is_int($e->getCode()) ? $e->getCode() : 500));
            }
        }
        
        /**
         * @param string $driver
         *
         * @return string
         */
        private function getDns($driver)
        {
            switch ($driver) {
                case 'sqlsrv':
                    $dns = "sqlsrv:Server=%s;Connect=%s;ConnectionPooling=0";
                    break;
                case 'dblib':
                    $dns = "dblib:host=%s;dbname=%s";
                    break;
                default:
                    $dns = "mysql:host=%s;dbname=%s";
                    break;
            }
            
            return $dns;
        }
        
        /**
         * @return array
         */
        private function getDefaultOptions()
        {
            return [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
            ];
        }
        
        /**
         * @return bool
         */
        public function isFailed()
        {
            return $this->failed;
        }
    }
}
