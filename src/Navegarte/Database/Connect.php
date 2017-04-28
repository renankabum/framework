<?php

/**
 * NAVEGARTE Networks
 *
 * @package   FrontEnd
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Database;

use PDO;

/**
 * Class Connect
 *
 * @package Navegarte\Database
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
abstract class Connect
{
  /**
   * @var null|PDO
   */
  private static $conn = null;
  
  /**
   * Connect constructor.
   *
   * Previne instanciar a class
   */
  private function __construct() { }
  
  /**
   * Previne a clonagem da class
   */
  private function __clone() { }
  
  /**
   * Previne a desserialização da class
   */
  private function __wakeup() { }
  
  /**
   * Connecta com o banco de dados com o pattern singleton
   *
   * @return PDO
   */
  private static function Connect()
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
        
        /**
         * Usa o banco configurado
         */
        if (!empty($database)) {
          static::$conn->exec("use {$database};");
        }
        
        /**
         * Configura o encoding
         */
        if (!empty($charset) && !empty($collation)) {
          static::$conn->exec("set names {$charset} collate {$collation}");
        }
      }
    } catch (\PDOException $e) {
      trigger_error("Problema ao connectar: <b>[{$e->getMessage()}]</b>", E_USER_ERROR);
      die;
    }
    
    return static::$conn;
  }
  
  /**
   * Get conexão PDO
   *
   * @return \PDO
   */
  public static function boot()
  {
    return static::Connect();
  }
}
