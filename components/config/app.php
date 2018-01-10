<?php

return [
    /*
     * ---------------------------
     *  Application Name
     * ---------------------------
     */
    'name' => 'Lite frame',
    /*
     * ---------------------------
     *  Application URL
     * ---------------------------
     * | This specifies the application URL. The application will try to detect the
     * | the URL automatically and will only use this as a fallback.
     */
    'url' => appEnv('APP_URL', 'http://localhost'),
    /*
     * ----------------------------
     *  Logger
     * ----------------------------
     * | This specifies how log files will be created.
     * | Accepted values are single, daily and weekly
     */
    'log' => appEnv('APP_LOG', 'daily'),
    /*
     * ----------------------------
     *  Application Environment
     * ----------------------------
     * | Set to local or production
     */
    'env' => appEnv('APP_ENV', 'local'),
    /*
     * ----------------------------
     *  Output Compression
     * ----------------------------
     * | This is experimental and may produce wrong output for responses
     * | containing inline javascript comments.
     * | Only turn this on if you know what you are doing.
     */
    'compress_output' => true,
    /*
     * ----------------------------
     *  Assets Folder Name
     * ----------------------------
     * | Name of assets folder.
     */
    'assets' => appEnv('APP_ASSETS', 'assets'),
    /*
     * ----------------------------
     *  Storage Folder Name
     * ----------------------------
     * | Name of storage folder.
     */
    'storage' => appEnv('APP_STORAGE', 'storage'),
    /*
     * ----------------------------
     *  Autoload settings
     * ----------------------------
     * |
     */
    "autoload" => [
        //Folders where autloader will look for files
        "classmap" => [
            "components/lib"
        ],
        //Namespace to folder mapping
        "psr-4" => [
//            "PHPMailer\PHPMailer" => "PHPMailer/src"
        ],
        //Other files to autoload
        "files" => [
        ]
    ],
];
