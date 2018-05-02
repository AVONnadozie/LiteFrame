<?php

namespace LiteFrame\Storage;

use Exception;

class Env
{
    protected static $instance;
    protected $env;

    
    protected function __construct()
    {
    }

    
    /**
     * Return singleton class instance.
     *
     * @return Env
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function set($key, $value)
    {
        return static::getInstance()->setValue($key, $value);
    }

    public static function get($key, $default = null)
    {
        return static::getInstance()->getValue($key, $default);
    }

    public function setValue($key, $value)
    {
        throw new Exception('Operation not yet supported');
    }

    public function getValue($key, $default = null)
    {
        $systemEnv = $this->getSystemEnv($key, $default);
        $appEnv = $this->getAppEnv($key, $systemEnv);
        
        return $appEnv;
    }

    public function getAppEnv($key, $default = null)
    {
        return appEnv($key, $default);
    }

    public function getSystemEnv($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        //Cast
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        //Remove quote
        if (strlen($value) > 1 && strStartsWith($value, '"') && strEndsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
    
    public function setSystemEnv($key, $value)
    {
        throw new Exception('Operation not yet supported');
        //Validate key and value
        
        
//        return putenv("$key=$value");
    }
}
