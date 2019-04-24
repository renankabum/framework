<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 14/03/2019 Vagner Cardoso
 */

namespace Core\Providers\Database {
    
    use Core\App;
    use Core\Helpers\Obj;
    
    /**
     * Class Database
     *
     * @method Statement getPdo()
     * @method Statement fetch($fetchStyle = null, $cursorOrientation = 0, $cursorOffset = 0)
     * @method Statement fetchColumn($column_number = 0)
     * @method Statement fetchObject($class_name = "stdClass", $ctor_args = array())
     * @method Statement fetchAll($fetchStyle = null, $fetchArgument = null, $ctorArgs = null)
     * @method Statement rowCount()
     * @method Statement errorCode()
     * @method Statement errorInfo()
     * @method Statement setAttribute($attribute, $value)
     * @method Statement getAttribute($attribute)
     * @method Statement columnCount()
     * @method Statement getColumnMeta($column)
     * @method Statement setFetchMode($mode, $params)
     * @method Statement nextRowset()
     * @method Statement closeCursor()
     * @method Statement debugDumpParams()
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
        public function __construct($driver)
        {
            try {
                // Carrega configurações
                $connections = config('database.connections');
                
                // Verifica driver
                if (empty($connections[$driver]) || !in_array($driver, \PDO::getAvailableDrivers())) {
                    throw new \InvalidArgumentException(
                        "Driver `{$driver}` is not configured or installed in the application.", E_ERROR
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
            $driver = ($driver ?: config('database.default'));
            
            if (empty(self::$instance[$driver])) {
                self::$instance[$driver] = new self($driver);
            }
            
            return self::$instance[$driver];
        }
        
        /**
         * @param string $driver mysql|dblib|sqlsrv
         *
         * @return $this
         * @throws \Exception
         */
        public function driver($driver)
        {
            if (!empty($driver)) {
                return Database::getInstance($driver);
            }
            
            return $this;
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
                $this->bindValues();
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
            $data = $this->toData($data);
            $values = [];
            
            // Previne os binds caso exista
            $this->bindings = [];
            
            // Monta os valores conforme se é um array multimensional ou um array simples
            if (!empty($data[0])) {
                foreach ($data as $i => $item) {
                    $data = ($this->emitEvent("{$table}:creating", $item) ?: $item);
                    $values[] = ':'.implode("_{$i}, :", array_keys($data))."_{$i}";
                    
                    foreach ($data as $k => $v) {
                        $this->setBindings(["{$k}_{$i}" => $v]);
                    }
                }
                
                $values = '('.implode("), (", $values).')';
            } else {
                $data = ($this->emitEvent("{$table}:creating", $data) ?: $data);
                $values = '(:'.implode(', :', array_keys($data)).')';
                $this->setBindings($data);
            }
            
            // Executa a query
            $columns = implode(', ', array_keys($data));
            $statement = "INSERT INTO {$table} ({$columns}) VALUES {$values}";
            $statement = $this->query($statement);
            
            // Evento tbName:created
            $this->emitEvent("{$table}:created", $this->lastInsertId());
            
            return $statement;
        }
        
        /**
         * @param string $table
         * @param array|object $data
         * @param string $condition
         * @param string|array $bindings
         *
         * @return mixed
         * @throws \Exception
         */
        public function update($table, $data, $condition, $bindings = null)
        {
            // Variávies
            $table = (string) $table;
            $data = $this->toData($data);
            $condition = (string) $condition;
            $set = [];
            
            // Verifica registro
            $updated = $this->read($table, $condition, $bindings)->fetch();
            if (empty($this->toData($updated))) {
                return false;
            }
            
            // Evento tbName:updating
            $data = ($this->emitEvent("{$table}:updating", $data) ?: $data);
            
            // Trata os dados passado para atualzar
            foreach ($data as $key => $value) {
                $bind = $key;
                $value = filter_var($value, FILTER_DEFAULT);
                
                // Atualiza os dados do updated
                if ($this->isFetchObject()) {
                    $updated->{$key} = $value;
                } else {
                    $updated[$key] = $value;
                }
                
                // Verifica se já existe algum bind igual
                if (!empty($this->bindings[$bind])) {
                    $uniqid = mt_rand(1, time());
                    $bind = "{$bind}_{$uniqid}";
                }
                
                $set[] = "{$key} = :{$bind}";
                $this->bindings[$bind] = $value;
            }
            
            $set = implode(', ', $set);
            
            // Executa a query
            $statement = "UPDATE {$table} SET {$set} {$condition}";
            $this->query($statement, $bindings);
            
            // Evento tbName:updated
            $this->emitEvent("{$table}:updated", $updated);
            
            return $updated;
        }
        
        /**
         * @param string $table
         * @param string $condition
         * @param string|array $bindings
         *
         * @return mixed
         * @throws \Exception
         */
        public function delete($table, $condition, $bindings = null)
        {
            // Variávies
            $table = (string) $table;
            $condition = (string) $condition;
            
            // Verifica registro
            $deleted = $this->read($table, $condition, $bindings)->fetch();
            if (empty($this->toData($deleted))) {
                return false;
            }
            
            // Evento tbName:deleting
            $this->emitEvent("{$table}:deleting", $deleted);
            
            // Executa a query
            $statement = "DELETE FROM {$table} {$condition}";
            $this->query($statement, $bindings);
            
            // Evento tbName:deleted
            $this->emitEvent("{$table}:deleted", $deleted);
            
            return $deleted;
        }
        
        /**
         * @param int $style
         *
         * @return bool
         */
        public function isFetchObject($style = null)
        {
            $allowed = [\PDO::FETCH_OBJ, \PDO::FETCH_CLASS];
            $fetchMode = $style ?: $this->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE);
            
            if (in_array($fetchMode, $allowed)) {
                return true;
            }
            
            return false;
        }
        
        /**
         * @param mixed $data
         * @param string $type
         *
         * @return array
         */
        public function toData($data, $type = 'array')
        {
            // Variávies
            $type = (string) ($type ?: 'array');
            
            switch ($type) {
                case 'array':
                    if (is_object($data)) {
                        $data = Obj::toArray($data);
                    }
                    break;
                
                case 'object':
                    if (is_array($data)) {
                        $data = Obj::fromArray($data);
                    }
                    break;
            }
            
            return $data;
        }
        
        /**
         * @param string $name
         * @param mixed ... (Opcional) Argumento(s)
         *
         * @return mixed
         */
        private function emitEvent($name = null)
        {
            $event = App::getInstance()
                ->resolve('event');
            
            if (!empty($name) && $event) {
                // Retorna o evento emitido
                $arguments = func_get_args();
                array_shift($arguments);
                
                return $event->emit(
                    (string) $name, ...$arguments
                );
            }
            
            return false;
        }
        
        /**
         * @param string|array $bindings
         */
        private function setBindings($bindings)
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
                    $this->bindings[$key] = filter_var(
                        $value, FILTER_DEFAULT
                    );
                }
            }
        }
        
        /**
         * Executa os bindings e trata os valores
         */
        private function bindValues()
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
        
        /**
         * @param string $method
         * @param mixed ...$arguments
         *
         * @return mixed
         */
        public function __call($method, $arguments)
        {
            if ($this->statement && method_exists($this->statement, $method)) {
                return $this->statement->{$method}(...$arguments);
            }
            
            throw new \BadMethodCallException(
                sprintf("Call to undefined method %s::%s()", get_class(), $method), E_USER_ERROR
            );
        }
    }
}
