<?php

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
