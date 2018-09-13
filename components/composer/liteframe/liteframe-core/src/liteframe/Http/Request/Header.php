<?php

namespace LiteFrame\Http\Request;

use LiteFrame\Http\Request;
use Symfony\Component\HttpFoundation\HeaderBag;

class Header
{
    protected static $instance;

    /**
     * Return singleton class instance.
     *
     * @return HeaderBag
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = Request::getInstance()->headers;
        }

        return static::$instance;
    }

    public static function set($name, $value)
    {
        $self = static::getInstance();
        return $self->set($name, $value);
    }

    public static function get($name, $default = null)
    {
        $self = static::getInstance();
        return $self->get($name, $default);
    }

    public function __set($name, $value)
    {
        $self = static::getInstance();
        return $self->set($name, $value);
    }

    public function __get($name)
    {
        $self = static::getInstance();
        return $self->get($name);
    }

    public function __call($name, $arguments)
    {
        $self = static::getInstance();
        return call_user_func([$self, $name], $arguments);
    }
}
