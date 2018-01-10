<?php

use LiteFrame\Http\Request;
use LiteFrame\Http\Response;
use LiteFrame\Http\Routing\Router;
use LiteFrame\View\View;

/**
 * Get environment setting.
 *
 * @param type $key
 * @param type $default
 *
 * @return string
 */
function appEnv($key, $default = null)
{
    if (!isset($GLOBALS['env'])) {
        $path = WD . normalizePath('/components/.env.php');
        if (file_exists($path)) {
            $GLOBALS['env'] = require $path;
        } else {
            return $default;
        }
    }

    return isset($GLOBALS['env'][$key]) ? $GLOBALS['env'][$key] : $default;
}

/**
 * Get application configuration.
 *
 * @param type $key
 * @param type $default
 *
 * @return string
 */
function config($key, $default = null)
{
    $keys = explode('.', $key);
    if (is_array($keys)) {
        $file = $keys[0];
        $keys = array_slice($keys, 1);
    } else {
        $file = $key;
    }

    $path = base_path("components/config/$file.php");
    if (!file_exists($path)) {
        return $default;
    }

    $value = (array) require $path;
    foreach ($keys as $key) {
        if (isset($value[$key])) {
            $value = $value[$key];
        } else {
            return $default;
        }
    }
    return $value;
}

/**
 * Include a view as string.
 *
 * @param type $path
 * @param type $data
 *
 * @return string
 */
function includeView($path, $data = [])
{
    $view = new View();

    return $view->fetch($path, $data);
}

/**
 * Return a view.
 *
 * @param type $path
 * @param type $data
 *
 * @return Response
 */
function view($path, $data = [])
{
    return Response::getInstance()->view($path, $data);
}

/**
 * Abort with response code.
 *
 * @param type $code
 * @param type $message
 */
function abort($code, $message = '')
{
    Response::getInstance()
            ->abort($code, $message);
}

/**
 * Abort with response code if condition is true.
 *
 * @param type $code
 * @param type $message
 */
function abort_if($condition, $code, $message = '')
{
    if ($condition) {
        abort($code, $message);
    }
}

/**
 * Abort with response code unless condition is true.
 *
 * @param type $code
 * @param type $message
 */
function abort_unless($condition, $code, $message = '')
{
    if (!$condition) {
        abort($code, $message);
    }
}

/**
 * Get current route.
 *
 * @return string
 */
function route($routeName = null, $params = array())
{
    if ($routeName) {
        return Router::getInstance()->route($routeName, $params);
    } else {
        return Request::getInstance()->getRoute();
    }
}

/**
 * Check if route is current route.
 *
 * @param type $route
 *
 * @return bool
 */
function isRoute($route)
{
    $r = route();
    if ($r) {
        return $r->getName() === $route || $r->getRouteURI() === $route;
    }

    return false;
}

/**
 * Get input from request.
 *
 * @param type $key
 * @param type $default
 *
 * @return mixed
 */
function input($key, $default = null)
{
    return Request::getInstance()
                    ->input($key, $default);
}

/**
 * Get request.
 *
 * @param type $key
 * @param type $default
 *
 * @return Request
 */
function request($key = null, $default = null)
{
    $request = Request::getInstance();
    if (!empty($key)) {
        $request->input($key, $default);
    }

    return $request;
}

/**
 * Get response.
 *
 * @param type $content
 * @param type $code
 *
 * @return Response
 */
function response($content = null, $code = 200)
{
    $response = Response::getInstance();
    if (!empty($content)) {
        $response->setContent($content, $code);
    }

    return $response;
}

/**
 * Dump data.
 *
 * @param type $data
 */
function d()
{
    if (isCLI()) {
        $args = func_get_args();
        foreach ($args as $value) {
            var_dump($value);
        }
        exit;
    } else {
        echo <<<'EOF'
<html><head><style>pre{border: lightgrey thin solid;border-radius: 5px;padding: 10px;background-color: #eee}</style></head><body>
EOF;
        $args = func_get_args();
        echo '<body>';
        foreach ($args as $value) {
            echo '<pre>' . PHP_EOL;
            var_dump($value) . '<br/><br/>';
            echo '</pre>';
        }
        die('</body></html>');
    }
}

/**
 * Get absolute URL to this path.
 *
 * @param type $path
 *
 * @return string
 */
function url($path = '/')
{
    $request = Request::getInstance();
    $host = rtrim($request->getAppURL(), '/');
    if (empty($path) || $path === '/') {
        return $host;
    } else {
        return $host . '/' . trim($path, '/');
    }
}

/**
 * Get asset URL.
 *
 * @param type $path
 *
 * @return string
 */
function asset($path = '')
{
    $folder = config('app.assets', 'assets');

    return url($folder . '/' . trim($path, '/'));
}

/**
 * Get storage URL.
 *
 * @param type $uri
 *
 * @return type
 */
function storage_url($uri = '/')
{
    $folder = config('app.storage', 'storage');

    return url($folder . '/' . trim($uri, '/'));
}

function storage_path($path = '', $public = true)
{
    if ($public) {
        $folder = config('app.storage', 'storage');

        return base_path($folder . DS . trim($path, DS));
    } else {
        return data_path($path);
    }
}

function data_path($path)
{
    return base_path('components/data/' . trim($path, DS));
}

/**
 * Get the path to the base of the application.
 *
 * @param string $path
 *
 * @return string
 */
function base_path($path = '')
{
    if ($path) {
        return WD . DS . trim(normalizePath($path), DS);
    }

    return WD;
}

/**
 * Require all files in a directory.
 *
 * @param type $dir
 * @param type $suffix
 */
function requireAll($dir, $recursive = true, $suffix = '.php')
{
    $ndir = normalizePath($dir);
    if (!file_exists($ndir)) {
        return;
    }

    $files = scandir($ndir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $dir . DS . $file;
        if ($recursive && is_dir($path)) {
            require_all($path, $recursive, $suffix);
        } elseif (empty($suffix) || strEndsWith($file, $suffix)) {
            require_once $path;
        }
    }
}

function normalizeUrl($url)
{
    return str_replace('\\', '/', urldecode($url));
}

function normalizePath($path)
{
    $cds = DS === '/' ? '\\' : '/';

    return str_replace($cds, DS, $path);
}

function appAutoloader($class)
{
    global $autoload_config;

    //Autoload paths (in order of importance)
    $defaultAutoloadPaths = $autoload_config['classmap'];
    $userAutoloadPaths = config('app.autoload.classmap');
    $autoloadPaths = array_merge($defaultAutoloadPaths, $userAutoloadPaths);

    //Check psr-4 configuration
    $defaultPsr4 = $autoload_config['psr-4'];
    $userPsr4 = config('app.autoload.psr-4', []);
    $psr4 = array_merge($userPsr4, $defaultPsr4);
    $psr4Path = null;
    foreach ($psr4 as $namespace => $folder) {
        $pos = strpos($class, $namespace);
        if ($pos !== false) {
            $folder = trim($folder, '/');
            $chunk = trim(substr($class, strlen($namespace)), '\\');
            $psr4Path = "$folder/$chunk";
            break;
        }
    }

    foreach ($autoloadPaths as $path) {
        if ($psr4Path) {
            $location = base_path("$path/$psr4Path.php");
        } else {
            $location = base_path("$path/$class.php");
        }
        if (is_file($location)) {
            require_once $location;
            return;
        }
    }
}

function getClassAndMethodFromString($string)
{
    if (!preg_match('/^\w+@\w+$/', $string)) {
        throw new Exception("Invalid string format $string, string must be in the format Class@method");
    }
    $parts = explode('@', $string);
    return $parts;
}

function isCLI()
{
    return php_sapi_name() == 'cli' || !http_response_code();
}

function appIsLocal()
{
    return appEnv('APP_ENV', 'production') === 'local';
}
