<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 15/04/2019 Vagner Cardoso
 */

namespace Core\Providers\Database {

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
     * @property \Core\Providers\Logger\Logger logger
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
        protected $driver;

        /**
         * @var string
         */
        protected $table;

        /**
         * @var string
         */
        protected $primaryKey;

        /**
         * @var int
         */
        protected $fetchStyle;

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
         * @var array
         */
        protected $bindings = [];

        /**
         * @var int
         */
        protected $limit = null;

        /**
         * @var int
         */
        protected $offset = null;

        /**
         * @var array|object
         */
        protected $data = [];

        /**
         * @var array
         */
        protected $reset = [];

        /**
         * @param int $fetchStyle
         *
         * @return array|$this
         * @throws \Exception
         */
        public function fetch($fetchStyle = null)
        {
            // Verifica o tipo padrão do fetch
            if (empty($fetchStyle) && $this->fetchStyle) {
                $fetchStyle = $this->fetchStyle;
            }

            if ($this->db->isFetchObject($fetchStyle)) {
                $fetchStyle = get_called_class();
            }

            // Executa a query e percorre os resultados
            $result = $this->execute()->fetch(
                $fetchStyle
            );

            if (!empty($result)) {
                if (method_exists($this, '_row')) {
                    $this->_row($result);
                }
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
            if (empty($fetchStyle) && $this->fetchStyle) {
                $fetchStyle = $this->fetchStyle;
            }

            if ($this->db->isFetchObject($fetchStyle)) {
                $fetchStyle = \PDO::FETCH_CLASS;
                $fetchArgument = get_called_class();
            }

            // Executa a query e percorre os resultados
            $results = $this->execute()->fetchAll(
                $fetchStyle, $fetchArgument
            );

            if (!empty($results)) {
                foreach ($results as $index => $row) {
                    if (method_exists($this, '_row')) {
                        $this->_row($row);
                    }

                    $results[$index] = $row;
                }
            }

            return $results;
        }

        /**
         * @param int $id
         * @param int $fetchStyle
         *
         * @return bool|array|$this
         * @throws \Exception
         */
        public function fetchById($id, $fetchStyle = null)
        {
            // Verifica id
            if (!empty($id)) {
                if (is_array($id)) {
                    $this->where(sprintf(
                        "AND {$this->table}.{$this->primaryKey} IN (%s)", implode(',', $id)
                    ));

                    return $this->fetchAll($fetchStyle);
                } else {
                    $this->where("AND {$this->table}.{$this->primaryKey} = :pkbyid", [
                        'pkbyid' => filter_var($id, FILTER_DEFAULT),
                    ]);
                }
            }

            // Verificação se existe condições (where)
            if (empty($this->where)) {
                return false;
            }

            return $this->fetch($fetchStyle);
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
            return (int) $this->select("COUNT({$column}) AS count")
                ->order('count DESC')->limit(1)
                ->execute()
                ->fetch(\PDO::FETCH_OBJ)
                ->count;
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
                        if (!in_array($property->getName(), array_merge([
                            'fetchStyle',
                            'driver',
                            'table',
                            'primaryKey',
                            'reset',
                            'data',
                        ], $notReset))) {
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
         * @return array
         */
        public function toArray()
        {
            if (is_object($this->data)) {
                return Obj::toArray($this->data);
            }

            return $this->data;
        }

        /**
         * @return object
         */
        public function toObject()
        {
            if (is_array($this->data)) {
                return Obj::fromArray($this->data);
            }

            return $this->data;
        }

        /**
         * @return array|$this
         * @throws \Exception
         */
        public function save()
        {
            // Variáveis
            $where = implode(' ', $this->where);
            $bindings = $this->bindings;
            $primaryValue = $this->getPrimaryValue();
            $this->reset();

            // Se existir o registro, atualiza
            if ($this->fetchById($primaryValue)) {
                if (!empty($primaryValue)) {
                    $where = "{$this->table}.{$this->getPrimaryKey()} = :pkid {$where}";
                    $bindings['pkid'] = $primaryValue;
                }

                return $this->db->update(
                    $this->table, $this->data,
                    "WHERE ".$this->normalizeProperty($where),
                    $bindings
                );
            }

            // Adiciona registro
            $this->db->create($this->table, $this->data);
            $primaryValue = $this->db->lastInsertId();

            // Limpa propriedades
            $this->clearProperties();

            // Caso tenha a chave única criada
            // retorne os dados referente a ela
            if (!empty($primaryValue)) {
                return $this->reset()
                    ->where($where, $bindings)
                    ->fetchById($primaryValue);
            }

            return $this->data;
        }

        /**
         * @return array|bool|$this
         * @throws \Exception
         */
        public function delete()
        {
            // Verifica primaryKey
            if (!empty($this->getPrimaryValue())) {
                $this->where[] = "AND {$this->table}.{$this->getPrimaryKey()} = :pkid ";
                $this->bindings['pkid'] = $this->getPrimaryValue();
            }

            // Monta where
            if (is_array($this->where)) {
                $this->where = $this->normalizeProperty(
                    implode(' ', $this->where)
                );
            }

            if (empty($this->where)) {
                throw new \InvalidArgumentException(
                    sprintf("[delete] `%s::where()` is empty.", get_called_class()),
                    E_USER_ERROR
                );
            }

            // Remove
            $deleted = $this->db->delete(
                $this->table, "WHERE {$this->where}", $this->bindings
            );

            // Limpa propriedades
            $this->clearProperties();

            return $deleted;
        }

        /**
         * @param array|object $data
         * @param bool $validate
         *
         * @return $this
         */
        public function data($data, $validate = true)
        {
            // Junta os dados
            $data = array_merge(
                $this->db->toData($this->data), $this->db->toData($data)
            );

            // Verifica se existe o método para
            // tratar os dados antes de usa-los
            if (method_exists($this, '_data')) {
                $this->_data($data, $validate);
            }

            // Remonta a propriedade já com os
            // dados devidamente tratados
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }

            return $this;
        }

        /**
         * @return \Core\Providers\Database\Statement
         * @throws \Exception
         */
        protected function execute()
        {
            if (empty($this->table)) {
                throw new \InvalidArgumentException(
                    sprintf("[execute] `%s::table` is empty.", get_called_class()),
                    E_USER_ERROR
                );
            }

            // Verifica se o método está criado e executa
            if (method_exists($this, '_conditions')) {
                $this->_conditions();
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
                $this->where = $this->normalizeProperty(implode(' ', $this->where));
                $sql .= "WHERE {$this->where} ";
            }

            // Group BY
            if (!empty($this->group) && is_array($this->group)) {
                $this->group = implode(', ', $this->group);
                $sql .= "GROUP BY {$this->group} ";
            }

            // Having
            if (!empty($this->having) && is_array($this->having)) {
                $this->having = $this->normalizeProperty(implode(' ', $this->having));
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
            $statement = $this->db->driver($this->driver)
                ->query($sql, $this->bindings);

            // Limpa as propriedades da classe
            $this->clearProperties();

            return $statement;
        }

        /**
         * @return string
         */
        protected function getPrimaryKey()
        {
            return $this->primaryKey;
        }

        /**
         * @return string
         */
        protected function getPrimaryValue()
        {
            return $this->{$this->getPrimaryKey()};
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
                if (!empty($condition) && !array_search($condition, $this->{$property})) {
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
        protected function normalizeProperty($string)
        {
            $chars = ['and', 'AND', 'or', 'OR'];

            foreach ($chars as $char) {
                $len = mb_strlen($char);

                if (mb_substr($string, 0, $len) === (string) $char) {
                    $string = trim(mb_substr($string, $len));
                }
            }

            return $string;
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
            $this->bindings = [];
            $this->limit = null;
            $this->offset = null;
            $this->reset = [];
        }

        /**
         * @param string $name
         * @param mixed $value
         */
        public function __set($name, $value)
        {
            // Caso seja object
            if ($this->db->isFetchObject($this->fetchStyle)) {
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
            // array
            if (is_array($this->data) && !empty($this->data[$name])) {
                return $this->data[$name];
            }

            // object
            if (is_object($this->data) && isset($this->data->{$name})) {
                return $this->data->{$name};
            }

            return App::getInstance()
                ->resolve($name);
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
         * @param string $name
         */
        public function __unset($name)
        {
            if (is_object($this->data)) {
                unset($this->data->{$name});
            } else {
                unset($this->data[$name]);
            }
        }
    }
}
