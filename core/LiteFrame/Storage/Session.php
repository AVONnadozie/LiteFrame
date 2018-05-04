<?php

namespace LiteFrame\Storage;

class Session
{
    private static $instance;
    private $started;

    protected function __construct()
    {
        //load session configurations here
        //Start session
        if (session_status() === PHP_SESSION_NONE) {
            $this->configure();
            $this->started = session_start();
        }
    }

    /**
     * Return singleton class instance.
     *
     * @return Session
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Set session value
     * @param type $key
     * @param type $value
     * @return Session
     */
    public static function set($key, $value)
    {
        return static::getInstance()->setValue($key, $value);
    }

    public static function get($key, $default = null)
    {
        return static::getInstance()->getValue($key, $default);
    }
    
    public static function has($key)
    {
        return static::getInstance()->hasValue($key);
    }
    
    public static function destroy()
    {
        return static::getInstance()->flush();
    }

    public function getValue($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    public function hasValue($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Set session value
     * @param type $key
     * @param type $value
     * @return $this
     */
    public function setValue($key, $value)
    {
        if ($this->started) {
            $_SESSION[$key] = $value;
        }
        return $this;
    }
    
    public function flush()
    {
        if ($this->started) {
            session_unset();
            session_destroy();
        }
    }

    private function configure()
    {
        //Set name
        session_name(config('session.name', 'LiteFrame_Session'));
        //Enable cookies for storing session
        ini_set('session.use_cookies', 1);
        //Disabled changing session id through URL like http://example.php?PHPSESSID=<session id>
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        //Rejects any session ID from user that doesn't match current one and creates new one
        ini_set('session.use_strict_mode', config('session.use_strict_mode', 1));

        $server = Server::getInstance();
        ini_set('session.cookie_secure', config('session.secure', $server->isHttps()));
        ini_set('session.cookie_httponly', config('session.httponly', true));
        ini_set('session.cookie_domain', config('session.domain', $server->getHostname()));
        //session lifetime
        ini_set('session.cookie_lifetime', config('session.lifetime', 0));
//        //Entropy file
////        ini_set('session.entropy_file', "/dev/urandom");
    }
}
