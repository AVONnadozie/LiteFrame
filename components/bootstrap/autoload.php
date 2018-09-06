<?php

/**
 * Alias for <code>DIRECTORY_SEPARATOR</code>.
 */
define('DS', DIRECTORY_SEPARATOR);

/*
 * Working Directory/Application root directory
 */
define('WD', getcwd());

define('CORE_DIR', realpath(__DIR__ . '/../composer/liteframe/liteframe-core/src'));
file_exists(CORE_DIR) or die('Could not locate core files, composer files maybe missing. Run composer install');

/*
  |--------------------------------------------------------------------------
  | Include Core Helper Files
  |--------------------------------------------------------------------------
  |
 */
$ch_dir = CORE_DIR . DS . 'helpers';
$ch_files = scandir($ch_dir);
foreach ($ch_files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $path = $ch_dir . DS . $file;
    require $path;
}


/*
  |--------------------------------------------------------------------------
  | Include User Helper Functions
  |--------------------------------------------------------------------------
  |
 */
$helpers_files = basePath('/components/helpers');
requireAll($helpers_files);

/*
  |--------------------------------------------------------------------------
  | Register The Composer Auto Loader
  |--------------------------------------------------------------------------
  |
  |
 */

$composer_files = basePath('/components/composer/autoload.php');
file_exists($composer_files) or die('Composer files missing, run composer install');
require_once $composer_files;

/*
  |--------------------------------------------------------------------------
  | Configure application autoloader
  |--------------------------------------------------------------------------
  |
 */
spl_autoload_register('appAutoloader');
