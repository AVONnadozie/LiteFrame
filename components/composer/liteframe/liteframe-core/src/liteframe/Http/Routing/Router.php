<?php

namespace LiteFrame\Http\Routing;

use Closure;
use Exception;
use LiteFrame\Http\Request;
use Traversable;

/**
 * A fork of https://github.com/dannyvankooten/AltoRouter.
 *
 * @author Victor Anuebunwa
 */
class Router
{
    protected static $instance;

    /**
     * @var array Array of all routes (incl. named routes)
     */
    protected $routes = array();

    /**
     * @var array Array of all named routes
     */
    protected $namedRoutes = array();

    /**
     * @var array Array of default match types (regex helpers)
     */
    protected $matchTypes = array(
        'i' => '[0-9]++',
        'a' => '[0-9A-Za-z]++',
        'h' => '[0-9A-Fa-f]++',
        '*' => '.+?',
        '**' => '.++',
        '' => '[^/\.]++',
    );
    
    public static $namespace = "Controllers";

    /**
     * Route group properties
     * @var array
     */
    public static $groupsProps = [
        'prefix' => [],
        'namespace' => [],
        'name' => [],
        'middlewares' => []
    ];

    const TARGET_REGEX = '/^\S+@\w+$/';

    /**
     * Create router in one call from config.
     *
     * @param array  $routes
     * @param string $basePath
     * @param array  $matchTypes
     */
    private function __construct($routes = array(), $matchTypes = array())
    {
        $this->addRoutes($routes);
        $this->addMatchTypes($matchTypes);
    }

    /**
     * Return singleton class instance.
     *
     * @return Router
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Retrieves all routes.
     * Useful if you want to process or display routes.
     *
     * @return array All routes
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Add multiple routes at once from array in the following format:.
     *
     *   $routes = array(
     *      array($method, $route, $target, $name)
     *   );
     *
     * @param array $routes
     *
     * @author Koen Punt
     *
     * @throws Exception
     */
    public function addRoutes($routes)
    {
        if (!is_array($routes) && !$routes instanceof Traversable) {
            throw new Exception('Routes should be an array or an instance of Traversable');
        }
        foreach ($routes as $route) {
            call_user_func_array(array($this, 'map'), $route);
        }
    }

    /**
     * Add named match types. It uses array_merge so keys can be overwritten.
     *
     * @param array $matchTypes The key is the name and the value is the regex
     */
    public function addMatchTypes($matchTypes)
    {
        $this->matchTypes = array_merge($this->matchTypes, $matchTypes);
    }

    /**
     * Map a route to a target.
     *
     * @param string $method One of 5 HTTP Methods, or a pipe-separated list of multiple HTTP Methods (GET|POST|PATCH|PUT|DELETE)
     * @param string $route  The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
     * @param mixed  $target The target where this route should point to. Can be anything
     * @param string $name   Optional name of this route. Supply if you want to reverse route this url in your application
     *
     * @throws Exception
     *
     * @return Route Route object
     */
    protected function map($method, $route, $target, $name = null, $middlewares = null)
    {
        $routeObj = new Route($route, $target);
        $routeObj->setName($name);
        $routeObj->setMiddlewares($middlewares);
        $routeObj->setHttpMethod($method);

        $this->routes[] = $routeObj;

        return $routeObj;
    }

    public function registerNamedRoute(Route $routeObj)
    {
        $name = $routeObj->getName();
        if ($name && isset($this->namedRoutes[$name])) {
            if ($routeObj !== $this->namedRoutes[$name]) {
                throw new Exception("Can not redeclare route '{$name}'");
            }
        } else {
            $this->namedRoutes[$name] = $routeObj;
        }
    }

    /**
     * Reversed routing.
     *
     * Generate the URL for a named route. Replace regexes with supplied parameters
     *
     * @param string $routeName The name of the route
     * @param array @params Associative array of parameters to replace placeholders with
     *
     * @return string The URL of the route with named parameters in place
     *
     * @throws Exception
     */
    public function route($routeName, array $params = array())
    {

        // Check if named route exists
        if (!isset($this->namedRoutes[$routeName])) {
            throw new Exception("Route '{$routeName}' does not exist.");
        }

        // Replace named parameters
        $route = $this->namedRoutes[$routeName];

        // prepend base path to route url again
        $url = url($route->getRouteURI());

        $regex = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';
        if (preg_match_all($regex, $route->getRouteURI(), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $index => $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if ($pre) {
                    $block = substr($block, 1);
                }

                if (isset($params[$param])) {
                    // Part is found, replace for param value
                    $url = str_replace($block, $params[$param], $url);
                } elseif ($optional && $index !== 0) {
                    // Only strip preceeding slash if it's not at the base
                    $url = str_replace($pre.$block, '', $url);
                } else {
                    // Strip match block
                    $url = str_replace($block, '', $url);
                }
            }
        }

        return rtrim($url, '/');
    }

    /**
     * Match a given Request Url against stored routes.
     *
     * @param string $requestUrl
     * @param string $requestMethod
     *
     * @return Route|bool Array with route information on success, false on failure (no match)
     */
    public function matchRequest(Request $request)
    {
        $requestUrl = $request->getRouteURL();
        $requestMethod = $request->getMethod();

        if (empty($this->routes)) {
            $routeFile = basePath('app/Routes/web.php');
            if (file_exists($routeFile)) {
                require_once $routeFile;
            } else {
                throw new Exception("Route definition file $routeFile does not exist");
            }
        }

        $params = array();
        $match = false;
        foreach ($this->routes as $handler) {
            $methods = $handler->getHttpMethod();
            $route = $handler->getRouteURI();

            $method_match = (stripos($methods, $requestMethod) !== false);

            // Method did not match, continue to next route.
            if (!$method_match) {
                continue;
            }

            if ($route === '*') {
                // * wildcard (matches all)
                $match = true;
            } elseif (isset($route[0]) && $route[0] === '@') {
                // @ regex delimiter
                $pattern = '`'.substr($route, 1).'`u';
                $match = preg_match($pattern, $requestUrl, $params) === 1;
            } elseif (($position = strpos($route, '[')) === false) {
                // No params in url, do string comparison
                $match = strcmp($requestUrl, $route) === 0;
            } else {
                // Compare longest non-param string with url
                if (strncmp($requestUrl, $route, $position) !== 0) {
                    continue;
                }
                $regex = $this->compileRoute($route);
                $match = preg_match($regex, $requestUrl, $params) === 1;
            }

            if ($match) {
                if ($params) {
                    foreach ($params as $key => $value) {
                        if (is_numeric($key)) {
                            unset($params[$key]);
                        }
                    }
                }
                $handler->setParameters($params);
                $handler->lock();
                $request->setRoute($handler);

                return $handler;
            }
        }

        return false;
    }

    /**
     * Compile the regex for a given route (EXPENSIVE).
     */
    private function compileRoute($route)
    {
        $regex = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';
        if (preg_match_all($regex, $route, $matches, PREG_SET_ORDER)) {
            $matchTypes = $this->matchTypes;
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($matchTypes[$type])) {
                    $type = $matchTypes[$type];
                }
                if ($pre === '.') {
                    $pre = '\.';
                }

                $optional = $optional !== '' ? '?' : null;

                //Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                        .($pre !== '' ? $pre : null)
                        .'('
                        .($param !== '' ? "?P<$param>" : null)
                        .$type
                        .')'
                        .$optional
                        .')'
                        .$optional;

                $route = str_replace($block, $pattern, $route);
            }
        }

        return "`^$route$`u";
    }

    /**
     * Route all GET request for a given route to a controller or closure.
     *
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function get($route, $target, $name = null, $middleware = null)
    {
        return static::getInstance()
                        ->map('GET', $route, $target, $name, $middleware);
    }

    /**
     * Route all POST request for a given route to a controller or closure.
     *
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function post($route, $target, $name = null, $middleware = null)
    {
        return static::getInstance()
                        ->map('POST', $route, $target, $name, $middleware);
    }
    
    /**
     * Route all PATCH request for a given route to a controller or closure.
     *
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function patch($route, $target, $name = null, $middleware = null)
    {
        return static::getInstance()
                        ->map('PATCH', $route, $target, $name, $middleware);
    }
    
    /**
     * Route all DELETE request for a given route to a controller or closure.
     *
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function delete($route, $target, $name = null, $middleware = null)
    {
        return static::getInstance()
                        ->map('DELETE', $route, $target, $name, $middleware);
    }
    
    /**
     * Route all PUT request for a given route to a controller or closure.
     *
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function put($route, $target, $name = null, $middleware = null)
    {
        return static::getInstance()
                        ->map('PUT', $route, $target, $name, $middleware);
    }

    /**
     * Route all request verbs/methods for a given route to a controller or closure.
     *
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function all($route, $target, $name = null, $middleware = null)
    {
        return static::getInstance()
                        ->map('GET|POST|DELETE|PUT|PATCH', $route, $target, $name, $middleware);
    }

    /**
     * Route all request verbs/methods matching the given methods for
     * the given route to a controller or closure.
     *
     * @param string       $methods    Request methods e.g GET|POST|DELETE
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function anyOf($methods, $route, $target, $name = null, $middleware = null)
    {
        return static::getInstance()
                        ->map($methods, $route, $target, $name, $middleware);
    }
    
    /**
     * Route all request verbs/methods matching the given methods for
     * the given route to a controller or closure.
     *
     * @param string       $methods    Request methods e.g GET|POST|DELETE
     * @param string       $route      Request route
     * @param mixed        $target     Target controller method or closure
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function matchAll(array $routes)
    {
        foreach ($routes as $mapping) {
            list($methods, $route, $target, $name, $middleware) = $mapping;
            static::getInstance()->map($methods, $route, $target, $name, $middleware);
        }
    }

    /**
     * Return a view for this route.
     *
     * @param string       $route      Request route
     * @param mixed        $view       View template
     * @param mixed        $data       View data
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function view($route, $view, $data = [], $name = null, $middleware = null)
    {
        $target = function () use ($view, $data) {
            return view($view, $data);
        };

        return static::get($route, $target, $name, $middleware);
    }

    /**
     * Permanently redirect route to another route or URL.
     *
     * @param string       $route      Request route
     * @param mixed        $to         Route or URL to redirect to
     * @param string       $name       (Optional) name of route
     * @param string|array $middleware (Optional) Middleware
     *
     * @return Route
     */
    public static function redirect($route, $to, $name = null, $middleware = null)
    {
        $target = function () use ($to) {
            if (!filter_var($to, FILTER_VALIDATE_URL)) {
                $to = static::getInstance()->route($to);
            }

            return redirect($to, 301);
        };

        return static::all($route, $target, $name, $middleware);
    }

    public static function group(array $options, Closure $closure)
    {
        $hash = uniqid('prop_');
        //Add options
        if (isset($options['prefix'])) {
            static::$groupsProps['prefix'][$hash] = $options['prefix'];
        }
        if (isset($options['namespace'])) {
            static::$groupsProps['namespace'][$hash] = $options['namespace'];
        }
        if (isset($options['name'])) {
            static::$groupsProps['name'][$hash] = $options['name'];
        }
        if (isset($options['middlewares'])) {
            static::$groupsProps['middlewares'][$hash] = (array) $options['middlewares'];
        }
        //Run closure
        $closure();
        //Remove options
        unset(static::$groupsProps['prefix'][$hash]);
        unset(static::$groupsProps['namespace'][$hash]);
        unset(static::$groupsProps['name'][$hash]);
        unset(static::$groupsProps['middlewares'][$hash]);
    }
}
