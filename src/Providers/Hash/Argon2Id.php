<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 15/04/2019 Vagner Cardoso
 */

namespace Core\Providers\Hash {
    
    /**
     * Class Argon2Id
     *
     * @package Core\Providers\Hash
     * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    class Argon2Id extends Argon
    {
        /**
         * @return int
         */
        protected function algorithm()
        {
            return PASSWORD_ARGON2ID;
        }
    }
}
