<?php

return [
    'driver' => appEnv('DB_DRIVER', 'mysql'),
    'host' => appEnv('DB_HOST', 'localhost'),
    'port' => appEnv('DB_PORT', 3306),
    'dbname' => appEnv('DB_NAME', 'liteframe'),
    'dbuser' => appEnv('DB_USER', 'root'),
    'dbpassword' => appEnv('DB_PASSWORD', ''),
];
