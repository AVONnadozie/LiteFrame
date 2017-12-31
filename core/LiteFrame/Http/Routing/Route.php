<?php

namespace LiteFrame\Http\Routing;

use Closure;
use Exception;

/**
 * Description of Route.
 *
 * @author Victor Anuebunwa
 */
class Route
{
    private $name;
    private $method = 'GET';
    private $routeURI = '/';
    private $target;
    private $middlewares = array();
    private $parameters;
    private $lock = false;

    public function __construct($routeURL, $target)
    {
        $this->setRouteURI($routeURL);
        $this->setTarget($target);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getRouteURI()
    {
        return $this->routeURI;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getTargetController()
    {
        $target = $this->getTarget();
        if ($target instanceof Closure ||
                !preg_match('/^\w+@\w+$/', $target)) {
            return null;
        }
        $parts = explode('@', $target);
        return isset($parts[0]) ? $parts[0] : null;
    }

    public function getTargetMethod()
    {
        $target = $this->getTarget();
        if ($target instanceof Closure ||
                !preg_match('/^\w+@\w+$/', $target)) {
            return null;
        }
        $parts = explode('@', $target);
        return isset($parts[1]) ? $parts[1] : null;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setRouteURI($routeURI)
    {
        if ($routeURI) {
            $this->routeURI = $routeURI === '/' ? $routeURI : trim($routeURI, '/');
        }

        return $this;
    }

    public function setTarget($target)
    {
        if (!$target instanceof Closure &&
                !preg_match('/^\w+@\w+$/', $target)) {
            throw new Exception("Invalid target string $target");
        }
        $this->target = $target;

        return $this;
    }

    public function setParameters($parameters)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->parameters = $parameters;

        return $this;
    }

    public function setName($name)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->name = $name;
        //Register
        Router::getInstance()->registerNamedRoute($this);

        return $this;
    }

    public function setMethod($method)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->method = $method;

        return $this;
    }

    public function setMiddlewares($middleware)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        if (is_array($middleware)) {
            $this->middlewares = $middleware;
        } else {
            $args = func_get_args();
            $this->middlewares = array();
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $this->middlewares[] = $arg;
                }
            }
        }

        return $this;
    }

    /**
     * Disallow further modification to this object.
     */
    public function lock()
    {
        $this->lock = true;
    }
}
