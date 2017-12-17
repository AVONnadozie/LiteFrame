<?php

namespace LiteFrame\Http;

/**
 * Description of Controller.
 *
 * @author Victor Anuebunwa
 */
class Controller
{
    /**
     * List of named middlewares.
     *
     * @param type $name
     *
     * @return type
     */
    protected function middleware($name)
    {
        $args = func_get_args();
        if (empty($args)) {
            return;
        }

        if (count($args) == 1 && !is_array($args[0])) {
            $args = [$args[0]];
        }

        foreach ($args as $name) {
            $middleware = config("middlewares.$name");
            if ($middleware instanceof Middleware) {
                $middleware::run();
            }
        }
    }
}
