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
     * | This allows you to configure setup files for namespaces.
     * | This is not a recommeneded way to autoload files but was added with RedBeanPHP in mind
     * | and only available as a core functionality.
     * | This allows us to load and run RedBeanPHP classes only when necessary
     */
    "boot" => [
        'RedBeanPHP' => 'liteframe/Database/DBSetup.php',
        'R' => 'liteframe/Database/DBSetup.php'
    ]
];
