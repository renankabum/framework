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
    
    /**
     * Class Connect
     *
     * @package Core\Connect
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Connect extends \PDO
    {
        /**
         * @var
         */
        protected static $instance;
        
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
            // Carrega configurações
            $driver = (empty($driver) ? config('database.default') : $driver);
            $connections = config('database.connections');
            
            try {
                // Verifica driver
                if (!array_key_exists($driver, $connections)) {
                    throw new \InvalidArgumentException("Driver `{$driver}` is not configured in the application.", E_ERROR);
                }
                
                // Configuração do driver
                $connection = $connections[$driver];
                
                // Dsn e opçoes padrão
                $dsn = sprintf($this->getDsn($driver), $connection['host'], $connection['database']);
                $options = $this->getDefaultOptions() + $options;
                
                // Realiza a conexão
                parent::__construct($dsn, $connection['username'], $connection['password'], $options);
                
                // Usa a datanase
                $this->exec("USE {$connection['database']};");
                
                // Verifica se tem o CHARSET e COLLATE configurado
                if (!empty($connection['charset'])) {
                    $exec = "SET NAMES {$connection['charset']}";
                    
                    if (!empty($connection['collation'])) {
                        $exec .= " COLLATE {$connection['collation']}";
                    }
                    
                    $this->exec("{$exec};");
                }
            } catch (\PDOException $e) {
                $this->failed = true;
                
                throw new \Exception("[CONNECT] :: {$e->getMessage()}", (is_string($e->getCode()) ? E_USER_ERROR : $e->getCode()));
            }
        }
        
        /**
         * @param string $driver
         *
         * @return string
         */
        protected function getDsn($driver)
        {
            switch ($driver) {
                case 'sqlsrv':
                    return "sqlsrv:Server=%s;Connect=%s;ConnectionPooling=0";
                    break;
                case 'dblib':
                    return "dblib:host=%s;dbname=%s";
                    break;
                default:
                    return "mysql:host=%s;dbname=%s";
                    break;
            }
        }
        
        /**
         * @return array
         */
        protected function getDefaultOptions()
        {
            return [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
            ];
        }
        
        /**
         * Recupera a instância da classe
         *
         * @param string $driver
         * @param array  $options
         *
         * @return \Core\Database\Connect
         * @throws \Exception
         */
        public static function getInstance($driver = null, array $options = [])
        {
            if (empty(self::$instance)) {
                self::$instance = new self($driver, $options);
            }
            
            return self::$instance;
        }
        
        /**
         * Cria a transação customizada
         *
         * @param \Closure $callback
         *
         * @return \Closure|mixed
         * @throws \Exception|\Throwable
         */
        public function transaction(\Closure $callback)
        {
            try {
                // Inicia a transação
                $this->beginTransaction();
                
                // Executa o bloco de código
                $callback = call_user_func($callback);
                
                // Envia as informações para o banco
                $this->commit();
                
                return $callback;
            } catch (\Exception $e) {
                $this->rollBack();
                
                throw $e;
            } catch (\Throwable $e) {
                $this->rollBack();
                
                throw $e;
            }
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
