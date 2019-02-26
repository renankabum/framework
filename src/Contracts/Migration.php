<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 26/02/2019 Vagner Cardoso
 */

namespace Core\Contracts {
    
    use Core\App;
    use Phinx\Migration\AbstractMigration;
    
    /**
     * Class Migration
     *
     * @package App\Models
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    abstract class Migration extends AbstractMigration
    {
        /**
         * @var string
         */
        protected $table;
        
        /**
         * @var string
         */
        protected $engine = 'InnoDB';
        
        /**
         * @var string
         */
        protected $collation = 'utf8mb4_general_ci';
        
        /**
         * @var string|bool
         */
        protected $primaryKey = false;
        
        /**
         * @var array
         */
        protected $primaryKeys = [];
        
        /**
         * @param string|null $tableName
         * @param array $options
         *
         * @return \Phinx\Db\Table
         * @throws \Exception
         */
        public function table($tableName = null, $options = [])
        {
            // Variávies
            $tableName = (!empty($tableName) ? $tableName : $this->table);
            
            // Verifica a tabela
            if (empty($tableName)) {
                throw new \Exception(
                    sprintf("Table not defined in %s.", get_class($this)),
                    E_ERROR
                );
            }
            
            // Retorna o método pai
            return parent::table($tableName, array_merge([
                'id' => $this->primaryKey,
                'engine' => $this->engine,
                'collation' => $this->collation,
                'primary_key' => $this->primaryKeys,
            ], $options));
        }
        
        /**
         * @return void
         * @throws \Exception
         */
        public function down()
        {
            $this->table($this->table)->drop()->save();
        }
        
        /**
         * @param string $name
         *
         * @return mixed
         */
        public function __get($name)
        {
            return App::getInstance()->resolve($name);
        }
    }
}
