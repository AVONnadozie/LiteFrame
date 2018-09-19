<?php

namespace LiteFrame\Http;

use LiteFrame\Http\Request\Header;
use LiteFrame\Http\Request\Server;
use LiteFrame\Http\Request\Session;
use LiteFrame\Http\Request\UploadedFile;
use LiteFrame\Http\Routing\Route;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use function config;

/**
 * Description of Request.
 *
 * @author Victor Anuebunwa
 */
class Request extends SymfonyRequest
{
    protected static $instance;
    protected $routeUrl;
    protected $route;
    protected $appURL;
    protected $baseDir;
    protected $ajax;
    protected $routeParams = [];

    /**
     * Return singleton class instance.
     *
     * @return Request
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = static::createFromGlobals();
        }

        return static::$instance;
    }

    /**
     * Get request route.
     *
     * @return string
     */
    public function getRouteURL()
    {
        if (empty($this->routeUrl)) {
            $param = trim(Server::get('REQUEST_URI'), '/');

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
            $this->baseDir = trim(str_replace('index.php', '', Server::get('SCRIPT_NAME')), '/');
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
                $this->appURL = $protocol . '://' . $hostname . '/' . $this->getBaseDir();
            }
        }

        return $this->appURL;
    }

    public function getProtocol()
    {
        return Server::getProtocol();
    }

    public function getHostname()
    {
        return $this->getHttpHost();
    }

    /**
     * Current Route object.
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute(Route $route)
    {
        $this->route = $route;
        //Check and add route parameters
        if ($route) {
            $this->routeParams = $route->getParameters();
            $this->attributes->add($this->routeParams);
        }
        return $this;
    }

    /**
     * Get request parameters
     * @param string $key
     * @param mixed $default
     * @return array|string
     */
    public function input($key = null, $default = null)
    {
        if ($key) {
            return $this->get($key, $default);
        } else {
            return $this->all();
        }
    }

    public function all()
    {
        return array_merge($this->query->all(), $this->request->all(), $this->attributes->all());
    }

    /**
     * Check if uploaded file exists
     * @param string $name
     * @return boolean
     */
    public function hasFile($name)
    {
        return $this->files->has($name);
    }

    /**
     * Get uploaded file
     * @param string $name
     * @return UploadedFile Uploaded file
     */
    public function file($name)
    {
        return new UploadedFile($name);
    }

    /**
     * Get response object
     * @return Response
     */
    public function response()
    {
        return Response::getInstance();
    }

    /**
     * Get session
     * @return Session
     */
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
        return Header::get('X-PJAX') == true;
    }

    public function ajax()
    {
        if (!isset($this->ajax)) {
            $rw = Header::get('HTTP_X_REQUESTED_WITH');
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
        $contentType = Header::get('CONTENT_TYPE');

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
        $acceptable = Header::get('Accept');

        return !empty($acceptable) && (stripos($acceptable, '/json') !== false ||
                stripos($acceptable, '+json') !== false);
    }

    public function __get($name)
    {
        return $this->input($name);
    }
}
