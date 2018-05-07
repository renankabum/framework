<?php

/**
 * VCWeb Networks <https://www.vagnercardosoweb.com.br/>
 *
 * @package   VCWeb Networks
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   MIT
 *
 * @copyright 2017-2018 Vagner Cardoso
 */

namespace Core\Database {
    
    /**
     * Class DatabaseException
     *
     * @package Core\Database
     * @author  Vagner Cardoso <vagnercardosoweb@gmail.com>
     */
    final class DatabaseException extends \Exception
    {
        /**
         * DatabaseException constructor.
         *
         * @param string          $message
         * @param int             $code
         * @param \Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, \Throwable $previous = null)
        {
            if (is_string($code)) {
                $code = 500;
            }
            
            parent::__construct($message, $code, $previous);
        }
    }
}
