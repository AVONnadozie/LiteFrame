<?php

function randomChars($length = 8)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $result = '';
    $strlen = strlen($chars);
    for ($i = 1; $i <= $length; ++$i) {
        $pos = rand(0, $strlen - 1);
        $result .= substr($chars, $pos, 1);
    }

    return $result;
}

function removeScripts($html)
{
    return preg_replace('#<\\s*/*\\s*script\\s*>#', '', $html);
}

function bcrypt($value, array $options = [])
{
    $cost = isset($options['rounds']) ? $options['rounds'] : 10;

    $hash = password_hash($value, PASSWORD_BCRYPT, ['cost' => $cost]);

    if ($hash === false) {
        throw new RuntimeException('Bcrypt hashing not supported.');
    }

    return $hash;
}
