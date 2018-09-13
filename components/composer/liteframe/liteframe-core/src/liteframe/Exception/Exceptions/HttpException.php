<?php


namespace LiteFrame\Exception\Exceptions;

class HttpException extends \Exception
{
    protected $httpCode;
    protected $message;

    public function __construct($httpCode, $message, $previous = null)
    {
        $this->httpCode = $httpCode;
        $this->message = $message;
        parent::__construct($message, 0, $previous);
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }
}
