<?php

namespace LiteFrame\Exception\Exceptions;

use Exception;
use Throwable;

/**
 * Description of UnsupportedOperation
 *
 * @author avonnadozie
 */
class UnsupportedOperation extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Operation not supported.';
        }
        parent::__construct($message, $code, $previous);
    }
}
