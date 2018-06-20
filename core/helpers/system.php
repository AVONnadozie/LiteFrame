<?php

use LiteFrame\Http\Request;
use LiteFrame\Http\Response;
use LiteFrame\Http\Routing\Router;
use LiteFrame\View\View;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Get environment setting.
 *
 * @param type $key
 * @param type $default
 *
 * @return string
 */
define('ENV_PATH', 'components/env.php');
function appEnv($key, $default = null)
{
    if (!isset($GLOBALS['env'])) {
        $path = basePath(ENV_PATH);
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

    $path = basePath("components/config/$file.php");
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
function includeView($path, $data = [], $return = false)
{
    $view = new View();

    $content = $view->fetch($path, $data);
    if ($return) {
        return $content;
    } else {
        echo $content;
    }
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
function input($key = null, $default = null)
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
    $args = func_get_args();
    $cutomStyles = array(
        'default' => 'background-color:#ecf0f3; border:lightgray solid thin; color:#757575; line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:99999; word-break: break-all',
        'num' => 'font-weight:bold; color:#1299DA',
        'const' => 'font-weight:bold',
        'str' => 'font-weight:bold; color:#56DB3A',
        'note' => 'color:#1299DA',
        'ref' => 'color:#A0A0A0',
        'public' => 'color:#FFFFFF',
        'protected' => 'color:#FFFFFF',
        'private' => 'color:#FFFFFF',
        'meta' => 'color:#B729D9',
        'key' => 'color:#56DB3A',
        'index' => 'color:#1299DA',
        'ellipsis' => 'color:#FF8400',
    );
    
    VarDumper::setHandler(function ($var) use ($cutomStyles) {
        $cloner = new VarCloner();
        if ('cli' === PHP_SAPI) {
            $dumper = new CliDumper();
        } else {
            $dumper =  new HtmlDumper();
            $dumper->setStyles($cutomStyles);
        }
        $dumper->dump($cloner->cloneVar($var));
    });
    
    foreach ($args as $value) {
        VarDumper::dump($value);
    }
    exit;
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
function publicStorageURL($uri = '/')
{
    $folder = config('app.storage', 'storage');

    return url($folder . '/' . trim($uri, '/'));
}

function storagePath($path = '', $section = 'public')
{
    $storage_path = config('app.storage', 'storage');
    if ($section) {
        $storage_path = nPath($storage_path, $section);
    }
    
    return basePath(nPath($storage_path, $path));
}

function privateStoragePath($path, $context = '')
{
    return storagePath(nPath($path, $context), 'private');
}

function publicStoragePath($path, $context = '')
{
    return storagePath(nPath($path, $context), 'public');
}

/**
 * Get the path to the base of the application.
 *
 * @param string $path
 * @return string
 */
function basePath($path = '')
{
    return nPath(WD, $path);
}

/**
 * Get the default path to application files.
 *
 * @param string $path
 *
 * @return string
 */
function appPath($path = '')
{
    return basePath('app/'.$path);
}

/**
 * Concatenates two paths
 * @param string $path
 * @param type $context
 *
 * @return string
 */
function nPath($path, $context = '')
{
    $path = rtrim(normalizePath($path), DS);
    if ($context) {
        $path .= DS . trim(normalizePath($context), DS);
    }
    return $path;
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
    $defaultAutoloadPaths = $autoload_config['folders'];
    $userAutoloadPaths = config('autoload.folders');
    $autoloadPaths = array_merge($defaultAutoloadPaths, $userAutoloadPaths);

    //Check psr-4 configuration
    $defaultPsr4 = $autoload_config['mapping'];
    $userPsr4 = config('autoload.mapping', []);
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
            $location = basePath("$path/$psr4Path.php");
        } else {
            $location = basePath("$path/$class.php");
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

/**
 * Escape and display value.
 *
 * @param type $string
 */
function e($string)
{
    echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
}
