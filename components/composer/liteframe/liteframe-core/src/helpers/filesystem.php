<?php

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

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).$sizes[$factor];
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
