<?php

namespace LiteFrame\Storage;

class Cookie {
    
    /**
     * Return singleton class instance.
     *
     * @return Session
     */
    public static function getInstance() {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

}
