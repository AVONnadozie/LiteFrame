<?php

return [
    'driver' => app_env('DB_DRIVER', 'mysql'),
    'host' => app_env('DB_HOST', 'localhost'),
    'port' => app_env('DB_PORT', 3306),
    'dbname' => app_env('DB_NAME', 'liteframe'),
    'dbuser' => app_env('DB_USER', 'root'),
    'dbpassword' => app_env('DB_PASSWORD', ''),
];
