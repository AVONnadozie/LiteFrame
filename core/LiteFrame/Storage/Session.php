<?php

namespace LiteFrame\Storage;

class Session
{
    private static $instance;

    public function __construct()
    {
        //load session configurations
        
        //Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    
    /**
     * Return singleton class instance.
     *
     * @return Session
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    public static function set($key, $value)
    {
        return self::getInstance()->set($key, $value);
    }
    
    public static function get($key, $default = null)
    {
        return self::getInstance()->get($key, $default);
    }
    
    public function getValue($key, $default = null)
    {
        return isset($_SESSION[$key])? $_SESSION[$key] : $default;
    }
    
    public function setValue($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }
}
