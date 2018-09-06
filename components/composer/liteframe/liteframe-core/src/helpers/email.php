<?php

/**
 * @param $email
 *
 * @return bool
 */
function isEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

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
    throw new \Exception("Invalid email: {$email}");
}
