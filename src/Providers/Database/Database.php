<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 06/03/2019 Vagner Cardoso
 */

namespace Core\Providers\Database {
    
    use Core\App;
    use Core\Helpers\Obj;
    
    /**
     * Class Database
     *
     * @package Core\Providers\Database
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Database extends \PDO
    {
        /**
         * @var \PDO
         */
        private static $instance;
        
        /**
         * @var Statement
         */
        private $statement;
        
        /**
         * @var array
         */
        private $bindings = [];
        
        /**
         * Bloqueia a construção da classe
         *
         * @param string $driver
         *
         * @throws \Exception
         */
        public function __construct($driver = null)
        {
            try {
                // Carrega configurações
                $driver = (empty($driver) ? config('database.default') : $driver);
                $connections = config('database.connections');
                
                // Verifica driver
                if (!array_key_exists($driver, $connections)) {
                    throw new \InvalidArgumentException(
                        "Driver `{$driver}` is not configured in the application.", E_ERROR
                    );
                }
                
                // Configuração do driver
                $connection = $connections[$driver];
                
                // Verifica se os dados tão preenchidos
                if (empty($connection['host']) || empty($connection['username'])) {
                    throw new \Exception(
                        "Connection setup is not complete.", E_ERROR
                    );
                }
                
                // Connecta no banco
                $dsn = (!empty($connection['dsn']) ? $connection['dsn'] : 'mysql:host=%s;dbname=%s');
                
                parent::__construct(
                    sprintf($dsn, $connection['host'], $connection['database']),
                    $connection['username'],
                    $connection['password'],
                    config('database.options', [])
                );
                
                // Muda a classe statement
                $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, [Statement::class, [$this]]);
                
                // Usa a datanase
                $this->exec("USE {$connection['database']};");
                
                // Verifica se tem o CHARSET e COLLATE configurado
                if (!empty($connection['charset'])) {
                    $exec = "SET NAMES {$connection['charset']}";
                    
                    if (!empty($connection['collation'])) {
                        $exec .= " COLLATE {$connection['collation']}";
                    }
                    
                    $this->exec($exec);
                }
            } catch (\PDOException $e) {
                throw $e;
            }
        }
        
        /**
         * Database constructor.
         *
         * @param string $driver
         *
         * @return $this
         * @throws \Exception
         */
        public static function getInstance($driver = null)
        {
            if (empty(self::$instance)) {
                self::$instance = new self($driver);
            }
            
            return self::$instance;
        }
        
        /**
         * @param \Closure $callback
         *
         * @return \Closure|mixed
         * @throws \Exception
         */
        public function transaction(\Closure $callback)
        {
            try {
                $this->beginTransaction();
                $callback = $callback($this);
                $this->commit();
                
                return $callback;
            } catch (\Exception $e) {
                $this->rollBack();
                
                throw $e;
            }
        }
        
        /**
         * @param string $statement
         * @param string|array $bindings
         * @param array $driverOptions
         *
         * @return Statement
         * @throws \Exception
         */
        public function query($statement, $bindings = null, $driverOptions = [])
        {
            try {
                if (empty($statement)) {
                    throw new \InvalidArgumentException(
                        'Query para execução `$statement` não pode ser vázia.', E_ERROR
                    );
                }
                
                // Execute
                $this->statement = $this->prepare($statement, $driverOptions);
                $this->setBindings($bindings);
                $this->execBindings();
                $this->statement->execute();
                
                return $this->statement;
            } catch (\PDOException $e) {
                throw $e;
            }
        }
        
        /**
         * @param string $table
         * @param string $condition
         * @param string|array $bindings
         *
         * @return Statement
         * @throws \Exception
         */
        public function read($table, $condition = null, $bindings = null)
        {
            // Executa a query
            $statement = "SELECT {$table}.* FROM {$table} {$condition}";
            
            return $this->query($statement, $bindings);
        }
        
        /**
         * @param string $table
         * @param array|object $data
         *
         * @return Statement
         * @throws \Exception
         */
        public function create($table, $data)
        {
            // Variávies
            $table = (string) $table;
            $data = (is_object($data) ? Obj::toArray($data) : $data);
            $values = [];
            $columns = (!empty($data[0]) ? $data[0] : $data);
            $columns = implode(', ', array_keys($columns));
            
            // Dispara evento tbName:creating
            // Verifica eventos
            if (config('database.events', false) == 'true') {
                if ($event = App::getInstance()->resolve('event')) {
                    $event->emit("{$table}:creating", $data);
                }
            }
            
            // Previne os binds caso exista
            $this->bindings = [];
            
            // Monta os valores conforme se é um array multimensional ou um array simples
            if (!empty($data[0])) {
                foreach ($data as $i => $item) {
                    $values[] = ':'.implode("_{$i}, :", array_keys($item))."_{$i}";
                    
                    foreach ($item as $k => $v) {
                        $this->setBindings(["{$k}_{$i}" => $v]);
                    }
                }
                
                $values = '('.implode("), (", $values).')';
            } else {
                $this->setBindings($data);
                $values = '(:'.implode(', :', array_keys($data)).')';
            }
            
            // Executa a query
            $statement = "INSERT INTO {$table} ({$columns}) VALUES {$values}";
            $statement = $this->query($statement);
            
            // Verifica eventos
            if (config('database.events', false) == 'true') {
                // Dispara evento tbName:created
                if ($event = App::getInstance()->resolve('event')) {
                    $event->emit("{$table}:created", $this->lastInsertId());
                }
            }
            
            return $statement;
        }
        
        /**
         * @param string $table
         * @param array|object $data
         * @param string $condition
         * @param string|array $bindings
         *
         * @return Statement
         * @throws \Exception
         */
        public function update($table, $data, $condition, $bindings = null)
        {
            // Variávies
            $table = (string) $table;
            $data = (is_object($data) ? Obj::toArray($data) : $data);
            $condition = (string) $condition;
            $set = [];
            
            // Verifica eventos
            if (config('database.events', false) == 'true') {
                // Dispara evento tbName:updating
                if ($event = App::getInstance()->resolve('event')) {
                    $event->emit("{$table}:updating", $data);
                }
            }
            
            // Trata os dados passado para atualzar
            foreach ($data as $key => $value) {
                $bind = $key;
                
                // Verifica se já existe algum bind igual
                if (!empty($this->bindings[$bind])) {
                    $uniqid = mt_rand(1, time());
                    $bind = "{$bind}_{$uniqid}";
                }
                
                $set[] = "{$key} = :{$bind}";
                $this->bindings[$bind] = filter_var($value, FILTER_DEFAULT);
            }
            
            $set = implode(', ', $set);
            
            // Executa a query
            $statement = "UPDATE {$table} SET {$set} {$condition}";
            $statement = $this->query($statement, $bindings);
            
            // Verifica eventos
            if (config('database.events', false) == 'true') {
                // Dispara evento tbName:updated
                if ($event = App::getInstance()->resolve('event')) {
                    $event->emit("{$table}:updated", $this->read($table, $condition, $bindings)->fetch());
                }
            }
            
            return $statement;
        }
        
        /**
         * @param string $table
         * @param string $condition
         * @param string|array $bindings
         *
         * @return Statement
         * @throws \Exception
         */
        public function delete($table, $condition, $bindings = null)
        {
            // Variávies
            $table = (string) $table;
            $condition = (string) $condition;
            
            // Verifica eventos
            if (config('database.events', false) == 'true') {
                // Recupera o registro a ser deletado
                $row = $this->read($table, $condition, $bindings)->fetch();
                
                // Dispara evento tbName:deleting
                if ($event = App::getInstance()->resolve('event')) {
                    $event->emit("{$table}:deleting", $row);
                }
            }
            
            // Executa a query
            $statement = "DELETE FROM {$table} {$condition}";
            $statement = $this->query($statement, $bindings);
            
            // Verifica eventos
            if (config('database.events', false) == 'true') {
                // Dispara evento tbName:deleted
                if ($event = App::getInstance()->resolve('event')) {
                    $event->emit("{$table}:deleted", (!empty($row) ? $row : []));
                }
            }
            
            return $statement;
        }
        
        /**
         * @return bool
         */
        public function isChangeFetch()
        {
            if (in_array($this->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE), [\PDO::FETCH_OBJ, \PDO::FETCH_CLASS])) {
                return true;
            }
            
            return false;
        }
        
        /**
         * @param string|array $bindings
         */
        protected function setBindings($bindings)
        {
            if (!empty($bindings)) {
                // Se for string da o parse e transforma em array
                if (is_string($bindings)) {
                    if (function_exists('mb_parse_str')) {
                        mb_parse_str($bindings, $bindings);
                    } else {
                        parse_str($bindings, $bindings);
                    }
                }
                
                // Filtra os valores dos bindings
                foreach ($bindings as $key => $value) {
                    $this->bindings[$key] = filter_var($value, FILTER_DEFAULT);
                }
            }
        }
        
        /**
         * Executa os bindings e trata os valores
         */
        protected function execBindings()
        {
            if (!$this->statement instanceof Statement) {
                throw new \RuntimeException(
                    "Propriedade `statement` não é uma instância de `\PDOStatement`.", E_USER_ERROR
                );
            }
            
            if (!empty($this->bindings) && is_array($this->bindings)) {
                foreach ($this->bindings as $key => $value) {
                    if (is_string($key) && in_array($key, ['limit', 'offset', 'l', 'o'])) {
                        $value = (int) $value;
                    }
                    
                    $value = ((empty($value) && $value != '0')
                        ? null
                        : filter_var($value, FILTER_DEFAULT));
                    
                    $this->statement->bindValue(
                        (is_string($key) ? ":{$key}" : ((int) $key + 1)),
                        $value,
                        (is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR)
                    );
                }
            }
            
            // Reseta os binds
            $this->bindings = [];
        }
    }
}
