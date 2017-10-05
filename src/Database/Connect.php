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

namespace Core\Database;

use PDO;

/**
 * Class Connect
 *
 * @package Core\Database
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class Connect
{
    /**
     * @var null|PDO
     */
    private static $conn = null;
    
    /**
     * @var bool
     */
    private static $fail = false;
    
    /**
     * Get conexão PDO
     *
     * @return \PDO
     */
    public static function boot()
    {
        return static::connect();
    }
    
    /**
     * Connecta com o banco de dados com o pattern singleton
     *
     * @return \PDO
     * @throws \Exception
     */
    private static function connect()
    {
        /**
         * Pega as configurações
         */
        $default = config('database.default');
        extract(config("database.connect.{$default}"), EXTR_SKIP);
        
        try {
            if (static::$conn == null) {
                
                /**
                 * Configura o DSN
                 */
                $dsn = isset($port) ? "mysql:host={$host};port={$port};dbname={$database}" : "mysql:host={$host};dbname={$database}";
                
                /**
                 * Cria a conexão
                 */
                static::$conn = new PDO($dsn, $username, $password);
                static::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                static::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                //static::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                
                /**
                 * Usa o banco configurado
                 */
                if (!empty($database)) {
                    static::$conn->exec("USE {$database};");
                }
                
                /**
                 * Configura o encoding
                 */
                if (!empty($charset) && !empty($collation)) {
                    static::$conn->exec("SET NAMES {$charset} COLLATE {$collation}");
                }
            }
        } catch (\PDOException $e) {
            static::$fail = true;
            
            throw new \Exception("Connect:: {$e->getMessage()}");
        }
        
        return static::$conn;
    }
    
    /**
     * Bind values to their parameters in the given statement
     *
     * @param \PDOStatement $statement
     * @param  array        $bindings
     *
     * @return void
     */
    public static function bindValues(\PDOStatement $statement, $bindings)
    {
        foreach ($bindings as $index => $place) {
            if ($index == 'limit' || $index == 'offset') {
                $place = (int) $place;
            }
            
            $statement->bindValue(
                is_string($index) ? ":{$index}" : (int) $index + 1, $place, is_int($place) ? \PDO::PARAM_INT : \PDO::PARAM_STR
            );
        }
    }
    
    /**
     * Connect constructor.
     *
     * Previne instanciar a class
     */
    private function __construct()
    {
    }
    
    /**
     * Previne a clonagem da class
     */
    private function __clone()
    {
    }
    
    /**
     * Previne a desserialização da class
     */
    private function __wakeup()
    {
    }
}
