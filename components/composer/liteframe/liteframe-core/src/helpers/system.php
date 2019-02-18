<?php /** @noinspection ALL */

use LiteFrame\Exception\Exceptions\HttpException;
use LiteFrame\Http\Middlewares\ValidateCSRFToken;
use LiteFrame\Http\Request;
use LiteFrame\Http\Response;
use LiteFrame\Http\Routing\Router;
use LiteFrame\View\View;

define('ENV_PATH', 'components/env.php');
/**
 * Get environment setting.
 *
 * @param string $key
 * @param string $default
 *
 * @return string|array
 */
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
 * @param string $key
 * @param string $default
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
 * @param string $name
 * @param array $data
 *
 * @param bool $return
 * @return string|null
 * @throws Exception
 */
function includeView($name, $data = [], $return = false)
{
    $content = View::fetch($name, $data);
    if ($return) {
        return $content;
    } else {
        echo $content;
    }
}

/**
 * Return a view.
 *
 * @param string $name
 * @param array $data
 *
 * @return Response
 */
function view($name, $data = [])
{
    return Response::getInstance()->view($name, $data);
}

/**
 * Abort with response code.
 *
 * @param int $code
 * @param string $message
 * @throws HttpException
 */
function abort($code, $message = '')
{
    throw new HttpException($code, $message);
}

/**
 * Abort with response code if condition is true.
 *
 * @param $condition
 * @param int $code
 * @param string $message
 * @throws HttpException
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
 * @param $condition
 * @param int $code
 * @param string $message
 * @throws HttpException
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
 * @param string $routeName
 * @param array $params
 * @return string|\LiteFrame\Http\Routing\Route
 * @throws Exception
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
 * @param string $route
 *
 * @return boolean
 * @throws Exception
 */
function isRoute($route)
{
    $r = Request::getInstance()->getRoute();
    if ($r) {
        return $r->getName() === $route || $r->getRouteURI() === $route;
    }

    return false;
}

/**
 * Get input from request.
 *
 * @param string $key
 * @param string $default
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
 * @param string $key
 * @param string $default
 *
 * @return Request|array|string
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
 * @param string $content
 * @param int $code
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
 * @param string $path
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
 * @param string $path
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
 * @param string $uri
 * @return string
 */
function publicStorageURL($uri = '/')
{
    $folder = config('app.storage', 'storage');

    return url($folder . '/' . trim($uri, '/'));
}

/**
 * @param null $path path to file in storage
 * @param null $context set to public to access public storage, this is the same as calling `publicStoragePath()` or
 * set to private to access private storage, this is the same as calling `privateStoragePath()`
 * @return string
 */
function storagePath($path = null, $context = null)
{
    $storage_path = config('app.storage', 'storage');
    if ($path) {
        $storage_path = nPath($storage_path, $path);
    }

    if($context){
        $callable = $context.'StoragePath';
        return $callable($storage_path);
    }else {
        return basePath($storage_path);
    }
}

function logsStoragePath($path = null)
{
    return storagePath(nPath('system/logs', $path));
}
function privateStoragePath($path = null)
{
    return storagePath(nPath('private', $path));
}

function publicStoragePath($path = null)
{
    return storagePath(nPath('public',$path));
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
 * @param string $context
 * @param string $filename
 *
 * @return string
 */
function nPath($context, $filename = '')
{
    $context = rtrim(fixPath($context), DS);
    if ($filename) {
        $context .= DS . trim(fixPath($filename), DS);
    }
    return $context;
}

/**
 * Replace all dots with directory separators
 * @param $path
 * @return mixed
 */
function dotToPath($path)
{
    return str_replace('.', DS, $path);
}

/**
 * Replace forward slashes (/) and backslashes (\) with dots (.)
 * @param $path
 * @return string|string[]|null
 */
function pathToDot($path)
{
    return str_replace(['/','\\'], '.', $path);
}

/**
 * Require all files in a directory.
 *
 * @param string $dir
 * @param bool $recursive
 * @param string $suffix
 */
function requireAll($dir, $recursive = true, $suffix = '.php')
{
    $nDir = fixPath($dir);
    if (!file_exists($nDir)) {
        return;
    }

    $files = scandir($nDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $dir . DS . $file;
        if ($recursive && is_dir($path)) {
            requireAll($path, $recursive, $suffix);
        } elseif (empty($suffix) || strEndsWith($file, $suffix)) {
            require_once $path;
        }
    }
}

/**
 * Replaces backslashes (\) with forward slashes (/) in URLs
 * @param $url
 * @return mixed
 */
function fixUrl($url)
{
    return str_replace('\\', '/', urldecode($url));
}

/**
 * Properly fixes the appropriate directory separator for the path
 * @param $path
 * @return mixed
 */
function fixPath($path)
{
    $cds = DS === '/' ? '\\' : '/';

    return str_replace($cds, DS, $path);
}

/**
 * Fixes / and \ issues in namespaces
 * @param $namespace
 * @param string $class
 * @return string
 */
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
 * @param string $className
 * @return object
 */
function arrayToObject(array $array, $className)
{
    return unserialize(sprintf(
                    'O:%d:"%s"%s', strlen($className), $className, strstr(serialize($array), ':')
    ));
}

/**
 * @param $string
 * @return array
 * @throws Exception
 */
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
 * @param string $string
 * @return string
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Get CSRF token field for forms
 * @return string
 * @deprecated
 */
function csrf_field(){
    return csrfField();
}

/**
 * Get CSRF token field for forms
 * @return string
 */
function csrfField()
{
    $key = ValidateCSRFToken::$tokenKey;
    return "<input name='{$key}' value='" . csrf_token() . "' hidden>";
}

/**
 * Get CSRF token
 * @return mixed
 * @deprecated
 */
function csrf_token(){
    return csrfToken();
}


/**
 * Get CSRF token
 * @return mixed
 */
function csrfToken()
{
    return ValidateCSRFToken::getSessionToken();
}

/**
 * Get HTTP message for the given HTTP code
 * @param $code
 * @return string
 */
function getHttpResponseMessage($code)
{
    return Response::getInstance()->getHttpResponseMessage($code);
}

/**
 * Return a redirect response
 * @param $new_location
 * @param int $code
 * @return Response
 */
function redirect($new_location, $code = 302)
{
    return response()->redirect($new_location, $code);
}
