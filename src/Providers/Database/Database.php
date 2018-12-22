<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Providers\Database {
    
    /**
     * Class Database
     *
     * @package Core\Providers\Database
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Database
    {
        /**
         * @var
         */
        protected static $instance = null;
        
        /**
         * @var \PDO
         */
        protected $pdo;
        
        /**
         * @var \PDOStatement
         */
        protected $statement;
        
        /**
         * @var array
         */
        protected $places = [];
        
        /**
         * Database constructor.
         *
         * @param string $driver
         *
         * @throws \Exception
         */
        public function __construct($driver = null)
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
                
                // Verifica se os dados tão preenchidos
                if (
                    empty($connection['host']) ||
                    empty($connection['username'])
                ) {
                    throw new \Exception("Connection setup is not complete.", E_ERROR);
                }
                
                // Connecta no banco
                $dsn = (!empty($connection['dsn']) ? $connection['dsn'] : 'mysql:host=%s;dbname=%s');
                
                $this->pdo = new \PDO(
                    sprintf($dsn, $connection['host'], $connection['database']),
                    $connection['username'],
                    $connection['password'],
                    config('database.options', [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                    ])
                );
                
                // Usa a datanase
                $this->pdo->exec("USE {$connection['database']};");
                
                // Verifica se tem o CHARSET e COLLATE configurado
                if (!empty($connection['charset'])) {
                    $exec = "SET NAMES {$connection['charset']}";
                    
                    if (!empty($connection['collation'])) {
                        $exec .= " COLLATE {$connection['collation']}";
                    }
                    
                    $this->pdo->exec("{$exec};");
                }
            } catch (\PDOException $e) {
                throw new \Exception("[DB] {$e->getMessage()}", (is_string($e->getCode()) ? E_USER_ERROR : $e->getCode()));
            }
        }
        
        /**
         * @param null $driver
         *
         * @return \Core\Providers\Database\Database
         * @throws \Exception
         */
        public static function connect($driver = null)
        {
            if (self::$instance === null) {
                self::$instance = new self($driver);
            }
            
            return self::$instance;
        }
        
        /**
         * @return bool
         */
        public function inTransaction()
        {
            return $this->pdo->inTransaction();
        }
        
        /**
         * @param \Closure $callback
         *
         * @return \Closure|mixed
         * @throws \Throwable
         */
        public function transaction(\Closure $callback)
        {
            try {
                $this->beginTransaction();
                $callback = call_user_func($callback);
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
        public function beginTransaction()
        {
            return $this->pdo->beginTransaction();
        }
        
        /**
         * @return bool
         */
        public function commit()
        {
            return $this->pdo->commit();
        }
        
        /**
         * @return bool
         */
        public function rollBack()
        {
            return $this->pdo->rollBack();
        }
        
        /**
         * @return string
         */
        public function lastInsertId()
        {
            return $this->pdo->lastInsertId();
        }
        
        /**
         * @param int $fetch_style
         *
         * @return mixed
         */
        public function fetch($fetch_style = null)
        {
            return $this->statement->fetch($fetch_style);
        }
        
        /**
         * @return string
         */
        public function queryString()
        {
            return $this->statement->queryString;
        }
        
        /**
         * @param string|object $class_name
         *
         * @return mixed
         */
        public function fetchObject($class_name = 'stdClass')
        {
            return $this->statement->fetchObject($class_name);
        }
        
        /**
         * @return int
         */
        public function rowCount()
        {
            $rowCount = $this->statement->rowCount();
            
            if ($rowCount === -1) {
                $rowCount = count($this->fetchAll());
            }
            
            return $rowCount;
        }
        
        /**
         * @param int $fetch_style
         * @param mixed $fetch_argument
         * @param array $ctor_args
         *
         * @return array
         */
        public function fetchAll($fetch_style = \PDO::FETCH_ASSOC, $fetch_argument = null, array $ctor_args = [])
        {
            if ($fetch_style === \PDO::FETCH_BOTH) {
                return $this->statement->fetchAll();
            } else if ($fetch_style === \PDO::FETCH_CLASS) {
                return $this->statement->fetchAll($fetch_style, $fetch_argument, $ctor_args);
            } else if (in_array($fetch_style, [\PDO::FETCH_ASSOC, \PDO::FETCH_NUM, \PDO::FETCH_OBJ])) {
                return $this->statement->fetchAll($fetch_style);
            } else {
                return $this->statement->fetchAll($fetch_style, $fetch_argument);
            }
        }
        
        /**
         * @param string $sql
         * @param string|array $places
         *
         * @return $this
         * @throws \Exception
         */
        public function query($sql, $places = null)
        {
            $sql = (string) $sql;
            
            if (empty($sql)) {
                throw new \InvalidArgumentException("It is not possible to execute the `->query` method without passing the sql.", E_ERROR);
            }
            
            // Atualiza os places
            $this->setPlaces($places);
            
            try {
                // Execute
                $this->execute($sql);
                
                return $this;
            } catch (\PDOException $e) {
                throw new \Exception("[QUERY] {$e->getMessage()}", (is_string($e->getCode()) ? E_USER_ERROR : $e->getCode()));
            }
        }
        
        /**
         * @param string|array $places
         */
        protected function setPlaces($places)
        {
            if (!empty($places)) {
                if (!is_array($places)) {
                    if (function_exists('mb_parse_str')) {
                        mb_parse_str($places, $this->places);
                    } else {
                        parse_str($places, $this->places);
                    }
                } else {
                    $this->places = (array) $places;
                }
            }
        }
        
        /**
         * @param string $sql
         * @param array $driverOptions
         */
        protected function execute($sql, array $driverOptions = [])
        {
            // Statement
            $this->statement = $this->pdo->prepare($sql, $driverOptions);
            $this->bindValues();
            $this->statement->execute();
            
            // Clear places
            $this->places = [];
        }
        
        /**
         *
         */
        protected function bindValues()
        {
            if ($this->statement instanceof \PDOStatement) {
                foreach ($this->places as $field => $value) {
                    if (in_array($field, ['limit', 'offset', 'l', 'o'])) {
                        $value = (int) $value;
                    }
                    
                    $value = ((empty($value) && $value != '0') ? null : $value);
                    
                    $this->statement->bindValue(
                        (is_string($field) ? ":{$field}" : ((int) $field + 1)),
                        $value,
                        (is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR)
                    );
                }
            }
        }
        
        /**
         * @param string $table
         * @param string $condition
         * @param string|array $places
         *
         * @return $this
         * @throws \Exception
         */
        public function read($table, $condition = null, $places = null)
        {
            $table = (string) $table;
            $condition = (string) $condition;
            
            // Atualiza os places
            $this->setPlaces($places);
            
            try {
                // Execute
                $this->execute("SELECT * FROM {$table} {$condition}");
                
                return $this;
            } catch (\PDOException $e) {
                throw new \Exception("[READ] {$e->getMessage()}", (is_string($e->getCode()) ? E_USER_ERROR : $e->getCode()));
            }
        }
        
        /**
         * @param string $table
         * @param array $data
         *
         * @return $this
         * @throws \Exception
         */
        public function create($table, array $data)
        {
            $table = (string) $table;
            $values = [];
            
            // Monta as colunas
            $columns = (!empty($data[0]) ? $data[0] : $data);
            $columns = implode(', ', array_keys($columns));
            
            // Monta os valores conforme se é um array multimensional ou um array simples
            if (!empty($data[0])) {
                foreach ($data as $i => $item) {
                    $values[] = ':'.implode("{$i}, :", array_keys($item)).$i;
                    
                    foreach ($item as $k => $v) {
                        $this->places["{$k}{$i}"] = $v;
                    }
                }
                
                $values = '('.implode("), (", $values).')';
            } else {
                $this->setPlaces($data);
                $values = '(:'.implode(', :', array_keys($data)).')';
            }
            
            try {
                // Execute
                $this->execute("INSERT INTO {$table} ({$columns}) VALUES {$values}");
                
                return $this;
            } catch (\PDOException $e) {
                throw new \Exception("[CREATE] {$e->getMessage()}", (is_string($e->getCode()) ? E_USER_ERROR : $e->getCode()));
            }
        }
        
        /**
         * @param string $table
         * @param array $data
         * @param string $condition
         * @param string|array $places
         *
         * @return $this
         * @throws \Exception
         */
        public function update($table, array $data, $condition, $places = null)
        {
            $table = (string) $table;
            $condition = (string) $condition;
            $set = [];
            
            if (empty($condition)) {
                throw new \InvalidArgumentException("It is not possible to execute the `->update` method without passing the condition.", E_ERROR);
            }
            
            // Atualiza os places
            $this->setPlaces($places);
            
            // Trata os dados passado para atualzar
            foreach ($data as $field => $value) {
                $time = '';
                
                if (!empty($this->places[$field])) {
                    $time = time();
                }
                
                $set[] = "{$field} = :{$field}{$time}";
                
                $this->places["{$field}{$time}"] = $value;
            }
            
            $set = implode(', ', $set);
            
            try {
                // Execute
                $this->execute("UPDATE {$table} SET {$set} {$condition}");
                
                return $this;
            } catch (\PDOException $e) {
                throw new \Exception("[UPDATE] {$e->getMessage()}", (is_string($e->getCode()) ? E_USER_ERROR : $e->getCode()));
            }
        }
        
        /**
         * @param string $table
         * @param string $condition
         * @param string|array $places
         *
         * @return $this
         * @throws \Exception
         */
        public function delete($table, $condition, $places = null)
        {
            $table = (string) $table;
            $condition = (string) $condition;
            
            if (empty($condition)) {
                throw new \InvalidArgumentException("It is not possible to execute the `->delete` method without passing the condition.", E_ERROR);
            }
            
            // Atualiza os places
            $this->setPlaces($places);
            
            try {
                // Execute
                $this->execute("DELETE FROM {$table} {$condition}");
                
                return $this;
            } catch (\PDOException $e) {
                throw new \Exception("[DELETE] {$e->getMessage()}", (is_string($e->getCode()) ? E_USER_ERROR : $e->getCode()));
            }
        }
    }
}
