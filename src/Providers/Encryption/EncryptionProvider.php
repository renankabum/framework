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

namespace Core\Providers\Encryption {

    use Core\Contracts\Provider;
    use Core\Helpers\Str;

    /**
     * Class EncryptionProvider
     *
     * @package Core\Providers\Encryption
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class EncryptionProvider extends Provider
    {
        /**
         * @return void
         */
        public function register()
        {
            /**
             * @return \Core\Providers\Encryption\Encryption
             */
            $this->container['encryption'] = function () {
                $config = config('app.encryption');

                if (empty($config['key'])) {
                    throw new \RuntimeException(
                        'No application encryption key has been specified.'
                    );
                }

                if (Str::startsWith($config['key'], 'base64:')) {
                    $config['key'] = base64_decode(substr($config['key'], 7));
                }

                return new Encryption($config['key'], $config['cipher']);
            };
        }
    }
}
