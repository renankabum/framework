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

namespace Core\Providers {

    use Core\Contracts\Provider;
    use Illuminate\Database\Capsule\Manager as Eloquent;
    use Illuminate\Pagination\Paginator;

    /**
     * Class EloquentProvider
     *
     * composer require illuminate/database
     * And uncomment in bootstrap/registers.php the eloquent provider
     *
     * @package Core\Providers
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class EloquentProvider extends Provider
    {
        /**
         * Registers services on the given container.
         *
         * @return \Illuminate\Database\Connection|\Illuminate\Database\Query\Builder
         */
        public function register()
        {
            /*
             * Instanceof ORM eloquent
             */
            $eloquent = new Eloquent();

            /*
             * Make the connection
             */
            foreach (config('database.connections') as $index => $config) {
                $eloquent->addConnection(config("database.connections.{$index}"), 'default');
            }

            /*
            * Initializes orm eloquent
            */
            $eloquent->setAsGlobal();
            $eloquent->bootEloquent();

            /**
             * @return \Illuminate\Database\Capsule\Manager
             */
            $container['db'] = function () use ($eloquent) {
                return $eloquent;
            };
        }

        /**
         * Register other services, such as middleware etc.
         *
         * @return void
         */
        public function boot()
        {
            $request = request();
            $currentPage = $request->getParam('page');

            Paginator::currentPageResolver(
                function () use ($currentPage) {
                    if (filter_var($currentPage, FILTER_VALIDATE_INT) !== false && (int) $currentPage >= 1) {
                        return $currentPage;
                    }

                    return 1;
                }
            );
        }
    }
}