<?php

namespace LiteFrame\Http\Routing;

use Closure;
use Exception;
use LiteFrame\Utility\Collection;

/**
 * Description of Route.
 *
 * @author Victor Anuebunwa
 */
class Route
{
    private $name;
    private $httpMethod = 'GET';
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

    public function getHttpMethod() {
        return $this->httpMethod;
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
                !preg_match(Router::TARGET_REGEX, $target)) {
            return null;
        }
        $parts = explode('@', $target);
        return isset($parts[0]) ? $parts[0] : null;
    }

    public function getTargetMethod()
    {
        $target = $this->getTarget();
        if ($target instanceof Closure ||
                !preg_match(Router::TARGET_REGEX, $target)) {
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
        //Add group prefix if any
        $groupPrefix = implode('/', Router::$groupsProps['prefix']);
        if ($groupPrefix) {
            //Remove duplicate forward slashes in prefix
            $prefixed = trim(preg_replace('/\/+/', '/', $groupPrefix), '/');
            $routeURI = $prefixed . '/' . trim($routeURI, '/');
        }

        if ($routeURI) {
            $this->routeURI = $routeURI === '/' ? $routeURI : trim($routeURI, '/');
        }

        return $this;
    }

    public function setTarget($target)
    {
        //Add to route group if any
        if (!$target instanceof \Closure) {
            $groupNamespace = implode('\\', Router::$groupsProps['namespace']);
            $target = $groupNamespace ? "$groupNamespace\\$target" : $target;
        }

        if (!$target instanceof Closure &&
                !preg_match(Router::TARGET_REGEX, $target)) {
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

        if (empty($name)) {
            return $name;
        }

        //Add group name if any
        $groupName = implode('', Router::$groupsProps['name']);
        $this->name = $groupName . $name;

        //Register
        Router::getInstance()->registerNamedRoute($this);

        return $this;
    }

    public function setHttpMethod($method) {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->httpMethod = $method;

        return $this;
    }

    public function setMiddlewares($middleware)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $curMiddlewares = [];
        if (is_array($middleware)) {
            $curMiddlewares = $middleware;
        } else {
            $args = func_get_args();
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $curMiddlewares[] = $arg;
                }
            }
        }

        //Add group middlewares if any
        $this->middlewares = (new Collection(Router::$groupsProps['middlewares']))
                        ->flatten()->toArray();
        foreach ($curMiddlewares as $value) {
            if (in_array($value, $this->middlewares)) {
                continue;
            }
            $this->middlewares[] = $value;
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
