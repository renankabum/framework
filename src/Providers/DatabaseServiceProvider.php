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

namespace Core\Providers;

use Core\Contracts\ServiceProviderAbstract;
use Core\Database\Create;
use Core\Database\Delete;
use Core\Database\Read;
use Core\Database\Update;
use Slim\Container;

/**
 * Class DatabaseServiceProvider
 *
 * @package Core\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class DatabaseServiceProvider extends ServiceProviderAbstract
{
    /**
     * Registers services on the given container.
     *
     * @param \Slim\Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        
        /**
         * @return \Core\Database\Create
         */
        $container['create'] = function () {
            if (empty($create)) {
                $create = new Create;
            }
            
            return $create;
        };
        
        /**
         * @return \Core\Database\Read
         */
        $container['read'] = function () {
            if (empty($read)) {
                $read = new Read;
            }
            
            return $read;
        };
        
        /**
         * @return \Core\Database\Update
         */
        $container['update'] = function () {
            if (empty($update)) {
                $update = new Update;
            }
            
            return $update;
        };
        
        /**
         * @return \Core\Database\Delete
         */
        $container['delete'] = function () {
            if (empty($delete)) {
                $delete = new Delete;
            }
            
            return $delete;
        };
    }
}
