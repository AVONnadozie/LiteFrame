<?php

/*
 * ----------------------------
 *  Core Autoload Settings
 * ----------------------------
 * |
 */

$autoload_config = [
    /*
     * ----------------------------
     *  Lookup folders
     * ----------------------------
     * | Folders where autloader will look for packages
     */
    "folders" => [
        'core',
        'core/toolkit',
        'app',
        'tests'
    ],
    
    /*
     * ----------------------------
     *  PSR-4 Namespace to folder mapping
     * ----------------------------
     * |
     */
    "mapping" => [
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
    /*
     * ----------------------------
     *  Files to autoload (auto include)
     * ----------------------------
     * |
     */
    "files" => [
    ]
];
