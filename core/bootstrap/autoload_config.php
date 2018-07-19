<?php

/*
 * ----------------------------
 *  Core Autoload Settings
 * ----------------------------
 * |
 */

return [
    /*
     * ----------------------------
     *  Lookup folders
     * ----------------------------
     * | Folders where autloader will look for packages
     */
    "folders" => [
        'core',
        'core/libraries',
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
        'GO' => "php-cron-scheduler/src/GO",
        /**
         * Cron Expression required by PHP Cron Scheduler
         */
        'Cron' => "cron-expression/src/Cron",
        /**
         * Form Validator
         */
        'FormValidator' => "php-form-validation/src",
        /**
         * Symfony Components
         */
        'Symfony\Component\VarDumper' => "symfony/var-dumper",
        'Symfony\Polyfill\Mbstring' => "symfony/polyfill-mbstring"
    ],
    /*
     * ----------------------------
     * Files to find namespaces (Not recommended)
     * ----------------------------
     * | This is for including files with multiple classes, it maps a file to multiple namespaces.
     * | This is not a recommeneded way to autoload file but was added with RedBeanPHP in mind
     * | and only available as a core functionality
     */
    "vendor_file" => [
        'RedBeanPHP' => 'core/LiteFrame/Database/DBSetup.php',
        'R' => 'core/LiteFrame/Database/DBSetup.php'
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
