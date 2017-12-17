<?php

namespace LiteFrame\Http;

use LogicException;

/**
 * Description of Request.
 *
 * @author Victor Anuebunwa
 */
final class Request
{
    protected static $instance;
    protected $routeUrl;
    protected $route;
    protected $appURL;
    protected $baseDir;
    protected $protocol;
    protected $hostname;
    protected $ajax;
    protected $content;

    /**
     * Return singleton class instance.
     *
     * @return Request
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get request route.
     *
     * @return string
     */
    public function getRouteURL()
    {
        if (empty($this->routeUrl)) {
            $param = trim($_SERVER['REQUEST_URI'], '/');

            // strip base directory from request url
            $base_dir = $this->getBaseDir();
            $param = substr($param, strlen($base_dir));

            // Strip query string (?a=b) from Request Url
            $path = trim(explode('?', $param)[0], '/');
            $this->routeUrl = strtolower($path);

            if (empty($this->routeUrl)) {
                $this->routeUrl = '/';
            }
        }

        return $this->routeUrl;
    }

    /**
     * Get application base directory.
     *
     * @return type
     */
    public function getBaseDir()
    {
        if (empty($this->baseDir)) {
            $this->baseDir = trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
        }

        return $this->baseDir;
    }

    /**
     * Get application URL.
     *
     * @return type
     */
    public function getAppURL()
    {
        if (empty($this->appURL)) {
            $hostname = $this->getHostname();
            $protocol = $this->getProtocol();
            if (empty($hostname) || empty($protocol)) {
                $this->appURL = config('app.url');
            } else {
                $this->appURL = $protocol.'://'.$hostname.'/'.$this->getBaseDir();
            }
        }

        return $this->appURL;
    }

    public function getProtocol()
    {
        if (empty($this->protocol)) {
            $this->protocol = $_SERVER['REQUEST_SCHEME'];
        }

        return $this->protocol;
    }

    public function getHostname()
    {
        if (empty($this->hostname)) {
            $this->hostname = $_SERVER['HTTP_HOST'];
        }

        return $this->hostname;
    }

    public function getMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }

    /**
     * Current Route object.
     *
     * @return Routing\Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute(Routing\Route $route)
    {
        $this->route = $route;

        return $this;
    }

    public function input($key, $default = null)
    {
        $route = $this->getRoute();
        if (isset($_POST[$key])) {
            $input = $_POST[$key];
        }
        //Check route parameters
        elseif ($route && isset($route->getParameters()[$key])) {
            $input = $route->getParameters()[$key];
        }
        //Check get parameters
        elseif (isset($_GET[$key])) {
            $input = $_GET[$key];
        } else {
            $input = $default;
        }

        return $input;
    }

    public function response()
    {
        return Response::getInstance();
    }

    public function session()
    {
        return Session::getInstance();
    }

    /**
     * Determine if the request is the result of an PJAX call.
     *
     * @return bool
     */
    public function pjax()
    {
        return Header::getInstance()->get('X-PJAX') == true;
    }

    public function ajax()
    {
        if (!isset($this->ajax)) {
            $rw = Header::getInstance()->get('HTTP_X_REQUESTED_WITH');
            $this->ajax = !empty($rw) && strtolower($rw) === 'xmlhttprequest';
        }

        return $this->ajax;
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        $contentType = Header::getInstance()->get('CONTENT_TYPE');

        return stripos($contentType, '/json') !== false ||
                stripos($contentType, '+json') !== false;
    }

    /**
     * Determine if the current request probably expects a JSON response.
     *
     * @return bool
     */
    public function expectsJson()
    {
        return ($this->ajax() && !$this->pjax()) || $this->wantsJson();
    }

    /**
     * Determine if the current request is asking for JSON in return.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = Header::getInstance()->get('Accept');

        return isset($acceptable[0]) && (stripos($acceptable[0], '/json') !== false ||
                stripos($acceptable[0], '+json') !== false);
    }

    /**
     * Returns the request body content.
     *
     * @param bool $asResource If true, a resource will be returned
     *
     * @return string|resource The request body content or a resource to read the body stream
     *
     * @throws LogicException
     *
     * @author Laravel
     */
    public function getContent($asResource = false)
    {
        $currentContentIsResource = is_resource($this->content);
        if (PHP_VERSION_ID < 50600 && false === $this->content) {
            throw new LogicException('getContent() can only be called once when using the resource return type and PHP below 5.6.');
        }

        if (true === $asResource) {
            if ($currentContentIsResource) {
                rewind($this->content);

                return $this->content;
            }

            // Content passed in parameter (test)
            if (is_string($this->content)) {
                $resource = fopen('php://temp', 'r+');
                fwrite($resource, $this->content);
                rewind($resource);

                return $resource;
            }

            $this->content = false;

            return fopen('php://input', 'rb');
        }

        if ($currentContentIsResource) {
            rewind($this->content);

            return stream_get_contents($this->content);
        }

        if (null === $this->content || false === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    public function __get($name)
    {
        return $this->input($name);
    }
}
