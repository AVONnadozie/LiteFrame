<?php

/**
 * Alias for <code>DIRECTORY_SEPARATOR</code>.
 */
define('DS', DIRECTORY_SEPARATOR);

/*
 * Working Directory/Application root directory
 */
define('WD', realpath(__DIR__.'/../..'));

/*
  |--------------------------------------------------------------------------
  | Include Core Helper Files
  |--------------------------------------------------------------------------
  |
 */
$ch_dir = WD.DS.'core'.DS.'helpers';
$ch_files = scandir($ch_dir);
foreach ($ch_files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $path = $ch_dir.DS.$file;
    require $path;
}

/*
  |--------------------------------------------------------------------------
  | Include Autoload Files
  |--------------------------------------------------------------------------
  | Autoload core files and user files
 */
$autoload_config = require __DIR__ . '/autoload_config.php';
$autoload_files = array_merge($autoload_config['files'], config('autoload.files', []));
foreach ($autoload_files as $file) {
    require_once basePath($file);
}


/*
  |--------------------------------------------------------------------------
  | Include User Helper Functions
  |--------------------------------------------------------------------------
  |
 */
requireAll(WD . '/components/helpers');

/*
  |--------------------------------------------------------------------------
  | Register The Composer Auto Loader if available
  |--------------------------------------------------------------------------
  |
  | Composer provides a convenient, automatically generated class loader
  | for our application. We just need to utilize it! We'll require it
  | into the script here so that we do not have to worry about the
  | loading of any our classes "manually". Feels great to relax.
  |
 */

$composer_files = WD . '/components/composer/autoload.php';
if (file_exists($composer_files)) {
    require_once $composer_files;
}

/*
  |--------------------------------------------------------------------------
  | Configure application autoloader
  |--------------------------------------------------------------------------
  |
 */
spl_autoload_register('appAutoloader');
