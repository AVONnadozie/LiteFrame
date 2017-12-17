<?php

namespace LiteFrame\Http;

/**
 * Description of Session.
 *
 * @author Victor Anuebunwa
 */
class Session
{
    private static $instance;

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
}
