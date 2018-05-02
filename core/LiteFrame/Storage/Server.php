<?php

namespace LiteFrame\Storage;

class Server
{
    protected static $instance;

    protected function __construct()
    {
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
    
    public function getServerValue($key, $default = null)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
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
}
