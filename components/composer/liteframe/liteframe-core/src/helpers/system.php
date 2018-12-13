<?php

use LiteFrame\Exception\Exceptions\HttpException;
use LiteFrame\Http\Middlewares\ValidateCSRFToken;
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
define('ENV_PATH', 'components/env.php');

function appEnv($key = null, $default = null)
{
    if (!isset($GLOBALS['env'])) {
        $path = basePath(ENV_PATH);
        if (file_exists($path)) {
            $GLOBALS['env'] = require $path;
        } else {
            return $default;
        }
    }

    if ($key) {
        return isset($GLOBALS['env'][$key]) ? $GLOBALS['env'][$key] : $default;
    } else {
        return $GLOBALS['env'];
    }
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
    $content = View::fetch($path, $data);
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
    throw new HttpException($code, $message);
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
        return $request->input($key, $default);
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


if (!function_exists('d')) {
    /**
     * Alias for dd()
     */
    function d()
    {
        call_user_func_array('dd', func_get_args());
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
        return fixUrl($host . '/' . trim($path, '/'));
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
 * Get the path to the base of the application.
 *
 * @param string $path
 * @return string
 */
function _corePath($path = '')
{
    return nPath(CORE_DIR, $path);
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
    return basePath('app/' . $path);
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
    $path = rtrim(fixPath($path), DS);
    if ($context) {
        $path .= DS . trim(fixPath($context), DS);
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
    $ndir = fixPath($dir);
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

function fixUrl($url)
{
    return str_replace('\\', '/', urldecode($url));
}

function fixPath($path)
{
    $cds = DS === '/' ? '\\' : '/';

    return str_replace($cds, DS, $path);
}

function fixClassname($namespace, $class = null)
{
    $ns = rtrim(str_replace('/', '\\', $namespace), '\\');
    if ($class) {
        $ns .= '\\' . trim(str_replace('/', '\\', $class), '\\');
    }

    $tns = trim($ns, '\\');
    return "\\$tns";
}

function appAutoloader($fullClassName)
{
    $autoload_config = require _corePath('bootstrap/autoload_config.php');
    //Load vendor boot files
    $file_mapping = isset($autoload_config['boot']) ? $autoload_config['boot'] : [];
    $vendor = isset(explode('\\', $fullClassName)[0]) ? explode('\\', $fullClassName)[0] : '';
    if ($vendor) {
        foreach ($file_mapping as $namespace => $file) {
            if ($vendor === $namespace) {
                require_once _corePath($file);
                break;
            }
        }
    }
    
    //Autoload paths (in order of importance)
    $autoloadPaths = $autoload_config['folders'];
    //Check psr-4 configuration
    $mappings = $autoload_config['mapping'];
    $mapPath = null;
    foreach ($mappings as $namespace => $folder) {
        $pos = strpos($fullClassName, $namespace);
        if ($pos !== false) {
            $folder = trim($folder, '/');
            $classname = trim(substr($fullClassName, strlen($namespace)), '\\');
            $mapPath = "$folder/$classname";
            break;
        }
    }

    foreach ($autoloadPaths as $path) {
        if ($mapPath) {
            $location = _corePath("$path/$mapPath.php");
        } else {
            $location = _corePath("$path/$fullClassName.php");
        }
        if (is_file($location)) {
            require_once $location;
            return;
        }
    }
}

/**
 * Class casting
 *
 * @param string|object $destination
 * @param object $sourceObject
 * @return object
 */
function cast($destination, $sourceObject)
{
    if (is_string($destination)) {
        $destination = new $destination();
    }
    $sourceReflection = new ReflectionObject($sourceObject);
    $destinationReflection = new ReflectionObject($destination);
    $sourceProperties = $sourceReflection->getProperties();
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($sourceObject);
        if ($destinationReflection->hasProperty($name)) {
            $propDest = $destinationReflection->getProperty($name);
            $propDest->setAccessible(true);
            $propDest->setValue($destination, $value);
        } else {
            $destination->$name = $value;
        }
    }
    return $destination;
}

/**
 * Copy array to object
 * @param array $array
 * @param type $className
 * @return type
 */
function arrayToObject(array $array, $className)
{
    return unserialize(sprintf(
                    'O:%d:"%s"%s',
        strlen($className),
        $className,
        strstr(serialize($array), ':')
    ));
}

/**
 * Copy object to another object
 * @param type $instance
 * @param type $className
 * @return type
 */
function objectToObject($instance, $className)
{
    return unserialize(sprintf(
                    'O:%d:"%s"%s',
        strlen($className),
        $className,
        strstr(strstr(serialize($instance), '"'), ':')
    ));
}

function getClassAndMethodFromString($string)
{
    if (!preg_match(Router::TARGET_REGEX, $string)) {
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
    return appEnv('APP_ENV', 'local') === 'local';
}

function appIsOnDebugMode()
{
    return appEnv('APP_DEBUG', true);
}

/**
 * Escape and display value.
 *
 * @param type $string
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
}

function csrf_field()
{
    $key = ValidateCSRFToken::$tokenKey;
    return "<input name='{$key}' value='" . csrf_token() . "' hidden>";
}

function csrf_token()
{
    return ValidateCSRFToken::getSessionToken();
}

function getHttpResponseMessage($code) {
    return Response::getInstance()->getHttpResponseMessage($code);
}

function redirect($new_location, $code = 302) {
    return response()->redirect($new_location, $code);
}
