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

    use Slim\Container;

    /**
     * Class StatementContainer
     *
     * @package Core\Database
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Statement
    {
        /**
         * @var \Slim\Container
         */
        protected $container;

        /**
         * @var string
         */
        protected $table;

        /**
         * @var string
         */
        protected $terms;

        /**
         * @var mixed
         */
        protected $result;

        /**
         * @var array
         */
        protected $places = [];

        /**
         * @var \PDOStatement
         */
        protected $stmt;

        /**
         * StatementContainer constructor.
         *
         * @param \Slim\Container $container
         */
        public function __construct(Container $container)
        {
            $this->container = $container;
        }

        /**
         * @param $places
         */
        protected function setPlaces($places)
        {
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

        /**
         * @param array $binds
         */
        protected function setBinds($binds)
        {
            foreach ((array) $binds as $key => $bind) {
                if ($key == 'limit' || $key == 'offset') {
                    $bind = (int) $bind;
                }

                $this->stmt->bindValue(is_string($key)
                    ? ":{$key}"
                    : (int) $key + 1, $bind, is_int($bind)
                    ? \PDO::PARAM_INT
                    : \PDO::PARAM_STR);
            }
        }

        /**
         * Executa o bind e query
         *
         * @param string $query
         *
         * @throws \Exception
         */
        protected function execute($query = null)
        {
            try {
                // Prepara a query
                $this->stmt = $this->container['db']->prepare($query ?: $this);

                // Binds values
                if (is_array($this->places) && !empty($this->places)) {
                    $this->setBinds($this->places);
                }

                // Executa a query
                $this->stmt->execute();
            } catch (\PDOException $e) {
                throw new \Exception($e->getMessage());
            }
        }

        /**
         * @return mixed
         */
        abstract public function __toString();
    }
}
