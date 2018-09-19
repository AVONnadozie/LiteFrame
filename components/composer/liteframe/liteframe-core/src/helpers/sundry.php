<?php

use LiteFrame\Database\Setting;

/**
 * @param $mixed
 *
 * @return bool
 */
function isJson($mixed)
{
    return is_string($mixed) and json_decode($mixed) and json_last_error() == JSON_ERROR_NONE;
}

function redirect($new_location, $code = 302)
{
    return response()->redirect($new_location, $code);
}

/**
 * Returns response as json if request is ajax or return type is json.<br/>
 * If a view is set and request is not ajax or return type is not json, the view
 * will be returned.<br/>.
 *
 * @param string $view
 * @param array  $data
 * @param bool   $cache       set true to set cache control to public
 * @param string $redirect_to URL to redirect to if every condition fails
 *
 * @return mixed
 */
function iResponse($view, $data = [], $cache = false, $redirect_to = null)
{
    if (request()->wantsJson() || request()->ajax()) {
        $response = response()->json($data);
    } elseif (!empty($view)) {
        $response = response()->view($view, $data);
    }

    if (isset($response)) {
        if ($cache) {
            return $response->header('cache-control', 'public');
        } else {
            return $response;
        }
    } elseif (!empty($redirect_to)) {
        return redirect($redirect_to);
    }

    return abort(404);
}

/**
 * More intelligent interface to system calls.
 *
 * @link http://php.net/manual/en/function.system.php
 *
 * @param $cmd
 * @param string $input
 *
 * @return array
 */
function iExec($cmd, $input = '')
{
    $process = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    fwrite($pipes[0], $input);
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $rtn = proc_close($process);

    return [
        'stdout' => $stdout,
        'stderr' => $stderr,
        'return' => $rtn,
    ];
}

function setting($key, $default = null, $cast = null)
{
    try {
        $value = appEnv($key);

        return $value ?: Setting::get($key, $default, $cast);
    } catch (Exception $e) {
        return $default;
    }
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

function copyright($startYear = null)
{
    $date = date('Y');
    $appName = config('app.name');
    if ($startYear && $date > $startYear) {
        $date = "$startYear - $date";
    }

    return "&copy; $date $appName. All rights reserved.";
}
