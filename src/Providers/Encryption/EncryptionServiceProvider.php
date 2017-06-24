<?php

/**
 * NAVEGARTE Networks
 *
 * @package   framework
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2017 Vagner Cardoso - NAVEGARTE
 */

namespace Navegarte\Providers\Encryption;

use Navegarte\Contracts\ServiceProviderAbstract;
use Navegarte\Helpers\Str;
use Slim\Container;

/**
 * Class EncryptionServiceProvider
 *
 * @package Navegarte\Providers\Encryption
 * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
final class EncryptionServiceProvider extends ServiceProviderAbstract
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
         * @return \Navegarte\Providers\Encryption\Encryption
         */
        $container['encryption'] = function () use ($container) {
            $config = config('app.encryption');
            
            if (empty($config['key'])) {
                throw new \RuntimeException(
                    'Nenhuma chave de criptografia de aplicativo foi especificada.'
                );
            }
            
            if (Str::startsWith($config['key'], 'base64:')) {
                $config['key'] = base64_decode(substr($config['key'], 7));
            }
            
            return new Encryption($container, $config['key'], $config['cipher']);
        };
    }
}
