<?php

namespace LiteFrame\Http;

/**
 * Description of Controller.
 *
 * @author Victor Anuebunwa
 */
class Controller
{
    private $middlewares = [];

    /**
     * Run middleware for this controller
     * @param type $name
     *
     * @return type
     */
    protected function middleware($name, $except = [])
    {
        $args = (array) $name;
        if (empty($args)) {
            return;
        }

        if (!empty($except)) {
            $route = request()->getRoute();
            $targetMethod = $route->getTargetMethod();

            foreach ($except as $method) {
                if ($targetMethod === $method) {
                    return;
                }
            }
        }

        foreach ($args as $name) {
            $this->middlewares[] = config("middlewares.$name");
        }
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }
    
    protected function validate($request, $rules)
    {
    }
}
