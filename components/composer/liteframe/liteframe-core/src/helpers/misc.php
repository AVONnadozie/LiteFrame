<?php

if (!function_exists('fancyCount')) {

    /**
     * Fancifully converts numbers to a more human friendly format. e.g 1000 = 1K
     * and 1000000 = 1M.
     *
     * @param float $number
     *
     * @return string
     */
    function fancyCount($number)
    {
        $sizes = ['K', 'M', 'B', 'Z'];
        if ($number < 1000) {
            return $number;
        }
        $i = intval(floor(log($number) / log(1000)));

        return round($number / pow(1000, $i), 2) . $sizes[$i - 1];
    }
}

if (!function_exists('fancyMaxCount')) {

    /**
     * Allows count only to max value.
     *
     * @param number $number
     * @param number $max
     *
     * @return string
     */
    function fancyMaxCount($number, $max = 99)
    {
        return $number > 99 ? "$max+" : "$number";
    }
}

if (!function_exists('formatNumber')) {

    /**
     * Format number
     * @param number $number number to format
     * @param number $dp decimal points
     *
     * @return string
     */
    function formatNumber($number, $dp = 0)
    {
        return number_format(floatval($number), $dp, '.', ',');
    }
}

if (!function_exists('toMoney')) {

    /**
     * Format money
     * @param number $number number to format
     * @param number $dp decimal points
     *
     * @return string
     */
    function toMoney($number, $sign = '$', $dp = 2)
    {
        return $sign . formatNumber($number, $dp);
    }
}

if (!function_exists('percentage')) {

    /**
     * Get percentage of a number
     *
     * @param number $number number to compute percentage
     * @param number $p percentage
     *
     * @return number
     */
    function percentage($number, $p = 100)
    {
        return $number * $p / 100;
    }
}


if (!function_exists('isEmail')) {

    /**
     * Validate email
     *
     * @param string $email
     *
     * @return bool
     */
    function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}


if (!function_exists('normalizeEmail')) {

    /**
     * validate and fix email address, removes dot from gmail addresses
     *
     * @param string $email
     *
     * @return string
     */
    function normalizeEmail($email)
    {
        if (isEmail($email)) {
            $parts = explode('@', strtolower($email));
            switch ($parts['1']) {
                case 'gmail.com': {
                        $parts[0] = str_replace('.', '', $parts[0]);
                    }
                    break;
            }

            return implode('@', $parts);
        }
        throw new Exception("Invalid email: {$email}");
    }
}


if (!function_exists('windows_os')) {

    /**
     * Determine whether the current environment is Windows based.
     *
     * @return bool
     */
    function windows_os()
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
    }
}

if (!function_exists('value')) {

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('trait_uses_recursive')) {

    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param string $trait
     *
     * @return array
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}

if (!function_exists('retry')) {

    /**
     * Retry an operation a given number of times.
     *
     * @param int      $times
     * @param callable $callback
     * @param int      $sleep
     *
     * @return mixed
     *
     * @throws Exception
     */
    function retry($times, callable $callback, $sleep = 0)
    {
        --$times;

        beginning:
        try {
            return $callback();
        } catch (Exception $e) {
            if (!$times) {
                throw $e;
            }

            --$times;

            if ($sleep) {
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }
}

if (!function_exists('preg_replace_array')) {

    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param string $pattern
     * @param array  $replacements
     * @param string $subject
     *
     * @return string
     */
    function preg_replace_array($pattern, array $replacements, $subject)
    {
        return preg_replace_callback($pattern, function () use (&$replacements) {
            foreach ($replacements as $key => $value) {
                return array_shift($replacements);
            }
        }, $subject);
    }
}

if (!function_exists('object_get')) {

    /**
     * Get an item from an object using "dot" notation.
     *
     * @param object $object
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if (!function_exists('last')) {

    /**
     * Get the last element from an array.
     *
     * @param array $array
     *
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (!function_exists('head')) {

    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param array $array
     *
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if (!function_exists('class_basename')) {

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param string|object $class
     *
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

/**
 * Credits: http://php.net/manual/en/function.filesize.php.
 *
 * @param $bytes
 * @param $decimals
 *
 * @return bool|int
 */
function bytesToSize($bytes, $decimals = 2)
{
    $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $sizes[$factor];
}

/**
 * Credits: http://php.net/manual/en/function.filesize.php.
 *
 * @param $url
 *
 * @return bool|int
 */
function bytesToSizeRemote($url)
{
    static $regex = '/^Content-Length: *+\K\d++$/im';
    if (!$fp = @fopen($url, 'rb')) {
        return false;
    }
    if (
            isset($http_response_header) &&
            preg_match($regex, implode("\n", $http_response_header), $matches)
    ) {
        return (int) $matches[0];
    }

    return strlen(stream_get_contents($fp));
}


function getCountryFlag($iso)
{
    $code = strtoupper($iso);

    return "http://www.geognos.com/api/en/countries/flag/$code.png";
}

function getRedirectUrl($url)
{
    stream_context_set_default(array(
        'http' => array(
            'method' => 'HEAD',
        ),
    ));

    $headers = get_headers($url, 1);
    if ($headers !== false && isset($headers['Location'])) {
        if (is_array($headers['Location'])) {
            return array_pop($headers['Location']);
        } else {
            return $headers['Location'];
        }
    }

    return false;
}

function copyright($startYear = null, $name = null)
{
    $date = date('Y');
    if (!$name) {
        $name = config('app.name');
    }
    if ($startYear && $date > $startYear) {
        $date = "$startYear - $date";
    }

    return "&copy; $date $name. All rights reserved.";
}

/**
 * @param mixed $find
 * @param mixed $replacements
 * @param mixed $subject
 *
 * @return mixed
 */
function strReplaceRecursive($find, $replacements, $subject)
{
    $num_replacements = 0;
    $subject = str_replace($find, $replacements, $subject, $num_replacements);
    if ($num_replacements == 0) {
        return $subject;
    } else {
        return strReplaceRecursive($find, $replacements, $subject);
    }
}

function isName($name, $alphanumeric = false)
{
    if ($alphanumeric) {
        return preg_match('/^[a-zA-Z0-9 ]*$/', $name);
    } else {
        return preg_match('/^[a-zA-Z ]*$/', $name);
    }
}

function searchWithWeight($haystack, $needle)
{
    if (starts_with($haystack, $needle)) {
        return 3;
    } elseif (stripos($haystack, $needle) !== false) {
        return 2;
    } else {
        try {
            $pattern = preg_replace('/\\s+/', '|', $needle);

            return preg_match("/{$pattern}/i", $haystack) ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}

function stripEmoji($text)
{
    $cleanText = '';

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $cleanText = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $cleanText = preg_replace($regexSymbols, '', $cleanText);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $cleanText = preg_replace($regexTransport, '', $cleanText);

    return $cleanText;
}

function strStartsWith($haystack, $needle)
{
    return stripos($haystack, $needle) === 0;
}

function strEndsWith($haystack, $needle)
{
    return strripos($haystack, $needle) === (strlen($haystack) - strlen($needle));
}

function zipStatusString($status)
{
    $msg = '';
    switch ((int) $status) {
        case ZipArchive::ER_OK: $msg = 'N No error';
            break;
        case ZipArchive::ER_MULTIDISK: $msg = 'N Multi-disk zip archives not supported';
            break;
        case ZipArchive::ER_RENAME: $msg = 'S Renaming temporary file failed';
            break;
        case ZipArchive::ER_CLOSE: $msg = 'S Closing zip archive failed';
            break;
        case ZipArchive::ER_SEEK: $msg = 'S Seek error';
            break;
        case ZipArchive::ER_READ: $msg = 'S Read error';
            break;
        case ZipArchive::ER_WRITE: $msg = 'S Write error';
            break;
        case ZipArchive::ER_CRC: $msg = 'N CRC error';
            break;
        case ZipArchive::ER_ZIPCLOSED: $msg = 'N Containing zip archive was closed';
            break;
        case ZipArchive::ER_NOENT: $msg = 'N No such file';
            break;
        case ZipArchive::ER_EXISTS: $msg = 'N File already exists';
            break;
        case ZipArchive::ER_OPEN: $msg = 'S Can\'t open file';
            break;
        case ZipArchive::ER_TMPOPEN: $msg = 'S Failure to create temporary file';
            break;
        case ZipArchive::ER_ZLIB: $msg = 'Z Zlib error';
            break;
        case ZipArchive::ER_MEMORY: $msg = 'N Malloc failure';
            break;
        case ZipArchive::ER_CHANGED: $msg = 'N Entry has been changed';
            break;
        case ZipArchive::ER_COMPNOTSUPP: $msg = 'N Compression method not supported';
            break;
        case ZipArchive::ER_EOF: $msg = 'N Premature EOF';
            break;
        case ZipArchive::ER_INVAL: $msg = 'N Invalid argument';
            break;
        case ZipArchive::ER_NOZIP: $msg = 'N Not a zip archive';
            break;
        case ZipArchive::ER_INTERNAL: $msg = 'N Internal error';
            break;
        case ZipArchive::ER_INCONS: $msg = 'N Zip archive inconsistent';
            break;
        case ZipArchive::ER_REMOVE: $msg = 'S Can\'t remove file';
            break;
        case ZipArchive::ER_DELETED: $msg = 'N Entry has been deleted';
            break;

        default: $msg = sprintf('Unknown status %s', $status);
            break;
    }

    return $msg;
}
