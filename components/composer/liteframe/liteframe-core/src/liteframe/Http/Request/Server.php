<?php

namespace LiteFrame\Http\Request;

use LiteFrame\CLI\Args;
use LiteFrame\Http\Request;

class Server
{
    protected static $instance;

    protected function __construct()
    {
    }

    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = Request::getInstance()->server;
        }

        return static::$instance;
    }

    public static function get($key, $default = null)
    {
        return static::getInstance()->get($key, $default);
    }

    public static function set($key, $value)
    {
        return static::getInstance()->set($key, $value);
    }

    public static function isSecure()
    {
        return static::isHttps();
    }
    
    public static function getProtocol()
    {
        return static::get('REQUEST_SCHEME');
    }

    public static function getHttpHost()
    {
        return static::get('HTTP_HOST');
    }

    public static function getMethod()
    {
        return static::get('REQUEST_METHOD');
    }

    public static function getPreviousURL()
    {
        return static::get('HTTP_REFERER');
    }

    public static function isHttps()
    {
        $value = static::get('HTTPS', 'off');
        //Translate
        return Args::booleanValue($value);
    }
}
