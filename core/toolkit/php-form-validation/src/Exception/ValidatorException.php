<?php

namespace FormValidator\Exception;

use Exception;

class ValidatorException extends Exception
{
    protected $_errors = array();
    
    public function __construct($message, array $errors = array()) {
        parent::__construct($message);
        $this->_errors = $errors;
    }
    
    public function getErrors() {
        return $this->_errors;
    }
}
