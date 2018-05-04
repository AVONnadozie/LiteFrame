<?php

namespace LiteFrame\Storage;

use LiteFrame\CLI\Args;

class Server
{
    protected static $instance;
    protected $values;

    protected function __construct()
    {
        $this->values = $_SERVER;
    }

    /**
     * Return singleton class instance.
     *
     * @return Server
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    
    public static function get($key, $default = null)
    {
        return static::getInstance()->getServerValue($key, $default);
    }
    
    public static function isSecure()
    {
        return static::getInstance()->isHttps();
    }
    
    public function getServerValue($key, $default = null)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $default;
    }
    
    
    public function getProtocol()
    {
        return  static::get('REQUEST_SCHEME', 'http');
    }

    public function getHostname()
    {
        return static::get('HTTP_HOST');
    }

    public function getMethod()
    {
        return static::get('REQUEST_METHOD', 'GET');
    }
    
    public function isHttps()
    {
        $value = static::get('HTTPS', 'off');
        //Translate
        return Args::$booleanMap[$value];
    }
}
