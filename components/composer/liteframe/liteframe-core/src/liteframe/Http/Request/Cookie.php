<?php

namespace LiteFrame\Http\Request;

use LiteFrame\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class Cookie
{
    protected static $instance;

    /**
     * Return singleton class instance.
     *
     * @return ParameterBag
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = Request::getInstance()->cookies;
        }

        return static::$instance;
    }

    public static function set($name, $value)
    {
        $self = static::getInstance();
        return $self->set($name, $value);
    }

    public static function get($name, $default)
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
