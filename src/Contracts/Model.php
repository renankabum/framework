<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 28/04/2017 Vagner Cardoso
 */

namespace Core\Contracts {
    
    use Core\App;
    use Core\Helpers\Obj;
    
    /**
     * Class Model
     *
     * @property \Slim\Collection settings
     * @property \Slim\Http\Environment environment
     * @property \Slim\Http\Request request
     * @property \Slim\Http\Response response
     * @property \Slim\Router router
     *
     * @property \Core\Providers\View\Twig view
     * @property \Core\Providers\Session\Session session
     * @property \Core\Providers\Session\Flash flash
     * @property \Core\Providers\Mailer\Mailer mailer
     * @property \Core\Providers\Hash\Bcrypt hash
     * @property \Core\Providers\Encryption\Encryption encryption
     * @property \Core\Providers\Jwt\Jwt jwt
     * @property \Core\Providers\Event\Event event
     *
     * @property \Core\Providers\Database\Database db
     *
     * @package Core\Contracts
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Model
    {
        /**
         * @var string
         */
        protected $table;
        
        /**
         * @var string
         */
        protected $primaryKey = 'id';
        
        /**
         * @var array
         */
        protected $select = [];
        
        /**
         * @var array
         */
        protected $join = [];
        
        /**
         * @var array
         */
        protected $where = [];
        
        /**
         * @var array
         */
        protected $group = [];
        
        /**
         * @var array
         */
        protected $having = [];
        
        /**
         * @var array
         */
        protected $order = [];
        
        /**
         * @var int
         */
        protected $limit = null;
        
        /**
         * @var int
         */
        protected $offset = null;
        
        /**
         * @var array
         */
        protected $data = [];
        
        /**
         * @var array
         */
        protected $reset = [];
        
        /**
         * @return array|$this
         * @throws \Exception
         */
        public function fetch()
        {
            // Verifica o tipo padrão do fetch
            if ($this->db->isChangeFetch()) {
                $result = $this->execute()->fetchObject(get_called_class());
            } else {
                $result = $this->execute()->fetch();
            }
            
            // Dispara evento
            if (method_exists($this, '_row')) {
                $this->{'_row'}($result);
            }
            
            return $result;
        }
        
        /**
         * @param int $fetchStyle
         * @param mixed $fetchArgument
         *
         * @return array[$this]
         * @throws \Exception
         */
        public function fetchAll($fetchStyle = null, $fetchArgument = null)
        {
            // Verifica o tipo padrão do fetch
            if ($this->db->isChangeFetch()) {
                $fetchStyle = \PDO::FETCH_CLASS;
                $fetchArgument = get_called_class();
            }
            
            // Executa a query e percorre os resultados
            $results = $this->execute()->fetchAll($fetchStyle, $fetchArgument);
            
            foreach ($results as $index => $row) {
                if (method_exists($this, '_row')) {
                    $this->{'_row'}($row);
                }
                
                $results[$index] = $row;
            }
            
            return $results;
        }
        
        /**
         * @param int $id
         *
         * @return array|$this
         * @throws \Exception
         */
        public function fetchById($id)
        {
            // Executa a query
            return $this->where("AND {$this->table}.{$this->primaryKey} = :pkbyid")
                ->bindings(['pkbyid' => filter_var($id, FILTER_DEFAULT)])
                ->limit(1)->fetch();
        }
        
        /**
         * @return int
         * @throws \Exception
         */
        public function rowCount()
        {
            return $this->execute()->rowCount();
        }
        
        /**
         * @param string $column
         *
         * @return int
         * @throws \Exception
         */
        public function count($column = '1')
        {
            // Executa a query
            $statement = $this->select("COUNT({$column}) AS count")
                ->order('count DESC')->limit(1)
                ->execute();
            
            if ($this->db->isChangeFetch()) {
                $result = $statement->fetchObject(get_called_class());
                
                return (int) $result->count;
            } else {
                $result = $statement->fetch();
                
                return (int) $result['count'];
            }
        }
        
        /**
         * @param string $table
         *
         * @return $this|string
         */
        public function table($table = null)
        {
            if (!empty($table)) {
                $this->table = (string) $table;
                
                return $this;
            }
            
            return $this->table;
        }
        
        /**
         * @param mixed $select
         *
         * @return $this
         */
        public function select($select)
        {
            if (is_string($select)) {
                $select = explode(',', $select);
            }
            
            $this->mountProperty($select, 'select');
            
            return $this;
        }
        
        /**
         * @param string|array|null $join
         *
         * @return $this
         */
        public function join($join)
        {
            $this->mountProperty($join, 'join');
            
            return $this;
        }
        
        /**
         * @param string|array $where
         * @param string|array $bindings
         *
         * @return $this
         */
        public function where($where, $bindings = null)
        {
            $this->mountProperty($where, 'where');
            $this->bindings($bindings);
            
            return $this;
        }
        
        /**
         * @param string|array|null $group
         *
         * @return $this
         */
        public function group($group)
        {
            $this->mountProperty($group, 'group');
            
            return $this;
        }
        
        /**
         * @param string|array|null $having
         *
         * @return $this
         */
        public function having($having)
        {
            $this->mountProperty($having, 'having');
            
            return $this;
        }
        
        /**
         * @param string|array|null $order
         *
         * @return $this
         */
        public function order($order)
        {
            $this->mountProperty($order, 'order');
            
            return $this;
        }
        
        /**
         * @param int $limit
         * @param int $offset
         *
         * @return $this
         */
        public function limit($limit, $offset = 0)
        {
            // Limit
            if (is_numeric($limit)) {
                $this->limit = (int) $limit;
                
                // Offset
                if (is_numeric($offset)) {
                    $this->offset($offset);
                }
            }
            
            return $this;
        }
        
        /**
         * @param int $offset
         *
         * @return $this
         */
        public function offset($offset)
        {
            $this->offset = (int) $offset;
            
            return $this;
        }
        
        /**
         * @param string|array $bindings
         *
         * @return $this
         */
        public function places($bindings)
        {
            return $this->bindings($bindings);
        }
        
        /**
         * @param string|array $bindings
         *
         * @return $this
         */
        public function bindings($bindings)
        {
            $this->db->setBindings($bindings);
            
            return $this;
        }
        
        /**
         * @param string|array|null $properties
         *
         * @return $this
         */
        public function reset($properties = [])
        {
            if (empty($properties)) {
                try {
                    $reflection = new \ReflectionClass(get_class($this));
                    
                    foreach ($reflection->getProperties() as $property) {
                        $notReset = (!empty($this->notReset) ? $this->notReset : []);
                        if (!in_array($property->getName(), array_merge(['table', 'primaryKey', 'reset', 'data'], $notReset))) {
                            $this->reset[$property->getName()] = true;
                        }
                    }
                } catch (\ReflectionException $e) {
                }
            } else {
                if (is_string($properties)) {
                    $properties = explode(',', $properties);
                }
                
                foreach ($properties as $property) {
                    $this->reset[trim($property)] = true;
                }
            }
            
            return $this;
        }
        
        /**
         * @return array|$this
         * @throws \Exception
         */
        public function save()
        {
            $id = (!empty($this->{$this->primaryKey}) ? $this->{$this->primaryKey} : null);
            
            if (!empty($id)) {
                // Verifica registro
                if (!$row = $this->reset()->fetchById($id)) {
                    throw new \Exception(
                        sprintf("Registro (#%d) não encontrado.", $id), E_USER_ERROR
                    );
                }
                
                // Where
                $condition = "WHERE {$this->table}.{$this->primaryKey} = :updid";
                
                if (!empty($this->where) && is_array($this->where)) {
                    $this->where = $this->normalizePropertyValue(implode(' ', $this->where));
                    $condition .= " {$this->where} ";
                    $this->where = [];
                }
                
                // Atualiza
                $this->db->update(
                    $this->table,
                    $this->data,
                    $condition,
                    ['updid' => $id]
                );
            } else {
                // Adiciona
                $id = $this->db->create($this->table, $this->data)->lastInsertId();
            }
            
            return $this->reset()->fetchById($id);
        }
        
        /**
         * @return array|bool|$this
         * @throws \Exception
         */
        public function delete()
        {
            $id = (!empty($this->{$this->primaryKey}) ? $this->{$this->primaryKey} : null);
            
            if (!empty($id)) {
                // Verifica registro
                if (!$row = $this->reset()->fetchById($id)) {
                    throw new \Exception(
                        sprintf("Registro (#%d) não encontrado.", $id), E_USER_ERROR
                    );
                }
                
                // Where
                $condition = "WHERE {$this->table}.{$this->primaryKey} = :delid";
                
                if (!empty($this->where) && is_array($this->where)) {
                    $this->where = $this->normalizePropertyValue(implode(' ', $this->where));
                    $condition .= " {$this->where} ";
                    $this->where = [];
                }
                
                // Atualiza
                $this->db->delete(
                    $this->table,
                    $condition,
                    ['delid' => $id]
                );
                
                return $row;
            }
            
            return false;
        }
        
        /**
         * @param array $data
         * @param bool $validate
         *
         * @return $this
         */
        public function data(array $data, $validate = true)
        {
            // Validações e tratamento dos dados
            if (method_exists($this, '_data')) {
                $this->_data($data, $validate);
            }
            
            // Monta a propriedade data com
            // os dados já tratados
            if ($this->db->isChangeFetch()) {
                $data = Obj::set($data);
            }
            
            $this->data = $data;
            
            return $this;
        }
        
        /**
         * @param string $name
         * @param mixed $value
         */
        public function __set($name, $value)
        {
            // Valor protegido
            $value = filter_var($value, FILTER_DEFAULT);
            
            // Caso seja object
            if ($this->db->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE) === \PDO::FETCH_OBJ) {
                if (!is_object($this->data)) {
                    $this->data = new \stdClass();
                }
                
                $this->data->{$name} = $value;
            } else {
                // Caso seja array
                if (!is_array($this->data)) {
                    $this->data = [];
                }
                
                $this->data[$name] = $value;
            }
        }
        
        /**
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            // Providers da aplicação
            if ($provider = App::getInstance()->resolve($name)) {
                return $provider;
            }
            
            // array
            if (is_array($this->data) && !empty($this->data[$name])) {
                return $this->data[$name];
            }
            
            // object
            if (is_object($this->data) && isset($this->data->{$name})) {
                return $this->data->{$name};
            }
            
            return false;
        }
        
        /**
         * @param string $name
         *
         * @return bool
         */
        public function __isset($name)
        {
            if (is_object($this->data)) {
                return isset($this->data->{$name});
            }
            
            return isset($this->data[$name]);
        }
        
        /**
         * @return \Core\Providers\Database\Statement
         * @throws \Exception
         */
        protected function execute()
        {
            if (empty($this->table)) {
                throw new \InvalidArgumentException(
                    'Propriedade `$this->table` não pode ser vázia.', E_USER_ERROR
                );
            }
            
            // Verifica se o método está criado e executa
            if (method_exists($this, '_conditions')) {
                $this->{'_conditions'}();
            }
            
            // Select
            $this->select = implode(', ', ($this->select ?: ["{$this->table}.*"]));
            $sql = "SELECT {$this->select} FROM {$this->table} ";
            
            // Join
            if (!empty($this->join) && is_array($this->join)) {
                $this->join = implode(' ', $this->join);
                $sql .= "{$this->join} ";
            }
            
            // Where
            if (!empty($this->where) && is_array($this->where)) {
                $this->where = $this->normalizePropertyValue(implode(' ', $this->where));
                $sql .= "WHERE {$this->where} ";
            }
            
            // Group BY
            if (!empty($this->group) && is_array($this->group)) {
                $this->group = implode(', ', $this->group);
                $sql .= "GROUP BY {$this->group} ";
            }
            
            // Having
            if (!empty($this->having) && is_array($this->having)) {
                $this->having = $this->normalizePropertyValue(implode(' ', $this->having));
                $sql .= "HAVING {$this->having} ";
            }
            
            // Order By
            if (!empty($this->order) && is_array($this->order)) {
                $this->order = implode(', ', $this->order);
                $sql .= "ORDER BY {$this->order} ";
            }
            
            // Limit & Offset
            if (!empty($this->limit) && is_int($this->limit)) {
                $this->offset = $this->offset ?: '0';
                
                if (in_array(config('database.default'), ['dblib', 'sqlsrv'])) {
                    $sql .= "OFFSET {$this->offset} ROWS FETCH NEXT {$this->limit} ROWS ONLY";
                } else {
                    $sql .= "LIMIT {$this->limit} OFFSET {$this->offset}";
                }
            }
            
            // Executa a query
            $statement = $this->db->query($sql);
            
            // Limpa as propriedades da classe
            $this->clearProperties();
            
            return $statement;
        }
        
        /**
         * Limpa as propriedade da classe para
         * uma nova consulta
         */
        protected function clearProperties()
        {
            $this->select = [];
            $this->join = [];
            $this->where = [];
            $this->group = [];
            $this->having = [];
            $this->order = [];
            $this->limit = null;
            $this->offset = null;
            $this->reset = [];
        }
        
        /**
         * Monta os array
         *
         * @param string|array|null $conditions
         * @param string $property
         */
        protected function mountProperty($conditions, $property)
        {
            if (!is_array($this->{$property})) {
                $this->{$property} = [];
            }
            
            foreach ((array) $conditions as $condition) {
                if (!empty($condition) &&
                    !array_search($condition, $this->{$property}) &&
                    !array_search("{$this->table}.{$condition}", $this->{$property})
                ) {
                    $this->{$property}[] = trim((string) $condition);
                }
            }
        }
        
        /**
         * Remove caracteres no começo da string
         *
         * @param $string
         *
         * @return string
         */
        protected function normalizePropertyValue($string)
        {
            $chars = ['and', 'AND', 'or', 'OR'];
            
            foreach ($chars as $char) {
                $strlenChar = mb_strlen($char);
                
                if (mb_substr($string, 0, $strlenChar) === (string) $char) {
                    $string = trim(mb_substr($string, $strlenChar));
                }
            }
            
            return $string;
        }
    }
}
