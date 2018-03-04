<?php

$autoload_config = [
    //Folders for autloader to look for files
    "classmap" => [
        'core',
        'core/toolkit',
        'app',
        'tests'
    ],
    //Namespace - Directory mapping
    "psr-4" => [
        /**
         * PHP Cron Scheduler
         */
        "GO\\" => "php-cron-scheduler/src/GO",
        /**
         * Cron Expression required by PHP Cron Scheduler
         */
        "Cron\\" => "cron-expression/src/Cron",
        /**
         * Form Validator
         */
        "FormValidator\\" => "php-form-validation/src",
    ],
    //Other files to autoload
    "files" => [
        /**
         * RedBean file
         */
    //        'core/toolkit/RedBeanPHP5_0_0/rb.php'
    ]
];
