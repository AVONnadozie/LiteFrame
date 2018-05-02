<?php

namespace LiteFrame\Storage;

class Server
{
    protected static $instance;


    /**
     * Return singleton class instance.
     *
     * @return Server
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    
    public static function get($key, $default = null)
    {
        return self::getInstance()->getServerValue($key, $default);
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
