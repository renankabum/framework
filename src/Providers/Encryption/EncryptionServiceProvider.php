<?php

/**
 * Core Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - Core
 */

namespace Core\Providers\Encryption;

use Core\Contracts\ServiceProviderAbstract;
use Core\Helpers\Str;
use Slim\Container;

/**
 * Class EncryptionServiceProvider
 *
 * @package Core\Providers\Encryption
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class EncryptionServiceProvider extends ServiceProviderAbstract
{
    /**
     * @param \Slim\Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        /**
         * @return \Core\Providers\Encryption\Encryption
         */
        $container['encryption'] = function () use ($container) {
            $config = config('app.encryption');
            
            if (empty($config['key'])) {
                throw new \RuntimeException(
                    'No application encryption key has been specified.'
                );
            }
            
            if (Str::startsWith($config['key'], 'base64:')) {
                $config['key'] = base64_decode(substr($config['key'], 7));
            }
            
            return new Encryption($container, $config['key'], $config['cipher']);
        };
    }
}
