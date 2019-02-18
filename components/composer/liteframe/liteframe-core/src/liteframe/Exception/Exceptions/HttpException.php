<?php

namespace LiteFrame\Exception\Exceptions;

class HttpException extends \Exception
{

    protected $httpCode;
    protected $message;
    protected $output;

    public function __construct($httpCode, $message, $previous = null)
    {
        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->output = $message ?: ($httpCode . ' ' . getHttpResponseMessage($httpCode));

        parent::__construct($this->output, 0, $previous);
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getPrimaryMessage()
    {
        return $this->message;
    }

    public function getOutputMessage()
    {
        return $this->output;
    }

}
