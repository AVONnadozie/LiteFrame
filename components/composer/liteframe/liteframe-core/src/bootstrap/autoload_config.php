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
        'libraries'
    ],
    /*
     * ----------------------------
     *  PSR-4 Namespace to folder mapping
     * ----------------------------
     * |
     */
    "mapping" => [
    ],
    /*
     * ----------------------------
     * Files to find namespaces (Not recommended)
     * ----------------------------
     * | This is for including files with multiple classes, it maps a file to multiple namespaces.
     * | This is not a recommeneded way to autoload file but was added with RedBeanPHP in mind
     * | and only available as a core functionality
     */
    "boot" => [
        'RedBeanPHP' => 'liteframe/Database/DBSetup.php',
        'R' => 'liteframe/Database/DBSetup.php'
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
