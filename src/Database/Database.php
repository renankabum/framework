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

namespace Core\Database {

    /**
     * Class Database
     *
     * @package Core\Database
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class Database extends \PDO
    {
        /**
         * @var bool
         */
        protected $failed;

        /**
         * Database constructor.
         *
         * @param string $driver
         * @param array  $options
         *
         * @throws \Exception
         */
        public function __construct($driver = null, array $options = [])
        {
            // Carrega as configurações
            $database = object_set(config('database'));

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

                throw new \Exception("Não foi possível conectar com o banco de dados.");
            }
        }

        /**
         * @return bool
         */
        public function isFailed()
        {
            return $this->failed;
        }

        /**
         * @return array
         */
        private function getDefaultOptions()
        {
            return [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ];
        }

        /**
         * @param string $driver
         *
         * @return string
         */
        private function getDns($driver)
        {
            if ($driver == 'sqlsrv') {
                $dns = "sqlsrv:Server=%s;Database=%s;ConnectionPooling=0";
            } elseif ($driver == 'dblib') {
                $dns = "dblib:host=%s;dbname=%s";
            } else {
                $dns = "mysql:host=%s;dbname=%s";
            }

            return $dns;
        }
    }
}
