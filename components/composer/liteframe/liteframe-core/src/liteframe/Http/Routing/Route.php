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
    private $middleware = array();
    private $parameters;
    private $lock = false;

    /**
     * Route constructor.
     * @param $routeURL
     * @param $target
     * @throws Exception
     */
    public function __construct($routeURL, $target)
    {
        $this->setRouteURI($routeURL);
        $this->setTarget($target);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getHttpMethod()
    {
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

    /**
     * Gets the matched controller class
     * @return string|null
     */
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

    /**
     * Gets the matched controller method
     * @return string|null
     */
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

    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Get route parameters
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets URI for this route
     * @param $routeURI
     * @return $this
     */
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

    /**
     * Sets the target controller or closure for this route
     * @param $target
     * @return $this
     * @throws Exception
     */
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

    /**
     * Sets parameters for this route
     * @param $parameters
     * @return $this
     * @throws Exception
     */
    public function setParameters($parameters)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Sets the name of this route and registers it in the Router
     * @param $name
     * @return $this
     * @throws Exception
     */
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

    /**
     * Sets the HTTP method for this route
     * @param $method
     * @return $this
     * @throws Exception
     */
    public function setHttpMethod($method)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->httpMethod = $method;

        return $this;
    }

    /**
     * Sets middleware for this route
     * @param $middleware
     * @return $this
     * @throws Exception
     */
    public function setMiddleware($middleware)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $curMiddleware = [];
        if (is_array($middleware)) {
            $curMiddleware = $middleware;
        } else {
            $args = func_get_args();
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $curMiddleware[] = $arg;
                }
            }
        }

        //Add group middleware if any
        $this->middleware = (new Collection(Router::$groupsProps['middleware']))
                        ->flatten()->toArray();
        foreach ($curMiddleware as $value) {
            if (in_array($value, $this->middleware)) {
                continue;
            }
            $this->middleware[] = $value;
        }

        return $this;
    }

    /**
     * Disallows further modification to this object.
     */
    public function lock()
    {
        $this->lock = true;
    }
}
