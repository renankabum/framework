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

namespace Navegarte\Providers;

use Navegarte\Contracts\ServiceProviderAbstract;
use Navegarte\Database\Create;
use Navegarte\Database\Delete;
use Navegarte\Database\Read;
use Navegarte\Database\Update;
use Slim\Container;

/**
 * Class DatabaseServiceProvider
 *
 * @package Navegarte\Providers
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class DatabaseServiceProvider extends ServiceProviderAbstract
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
         * @return \Navegarte\Database\Create
         */
        $container['create'] = function () {
            if (empty($create)) {
                $create = new Create;
            }
            
            return $create;
        };
        
        /**
         * @return \Navegarte\Database\Read
         */
        $container['read'] = function () {
            if (empty($read)) {
                $read = new Read;
            }
            
            return $read;
        };
        
        /**
         * @return \Navegarte\Database\Update
         */
        $container['update'] = function () {
            if (empty($update)) {
                $update = new Update;
            }
            
            return $update;
        };
        
        /**
         * @return \Navegarte\Database\Delete
         */
        $container['delete'] = function () {
            if (empty($delete)) {
                $delete = new Delete;
            }
            
            return $delete;
        };
    }
}
