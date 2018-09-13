<?php

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
        throw new \Exception("Invalid email: {$email}");
    }
}
