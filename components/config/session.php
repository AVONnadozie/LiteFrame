<?php

return [
    /*
     * ----------------
     * Session Name
     * ----------------
     */
    'name' => str_replace(' ', '_', config('app.name')) . '_Session',
    /*
     * -----------------
     * Session domain
     * -----------------
     */
    'domain' => LiteFrame\Storage\Server::get('HTTP_HOST'),
    /*
     * -----------------
     * Session Lifetime
     * -----------------
     * Default is 0, which makes it valid until browser is closed
     */
    'lifetime' => 0,
    /*
     * ----------------
     * Secure Session
     * ----------------
     */
    'secure' => LiteFrame\Storage\Server::isSecure(),
    /*
     * ----------------
     * Strict Mode
     * ----------------
     * Block uninitialized session id
     */
    'use_strict_mode' => true,
    /*
     * ----------------
     * Http Only
     * ----------------
     * Prevent client side access to cookie
     */
    'httponly' => true,
];
