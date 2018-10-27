<?php

/**
 * Alias for <code>DIRECTORY_SEPARATOR</code>.
 */
define('DS', DIRECTORY_SEPARATOR);

/*
 * Working Directory/Application root directory
 */
define('WD', getcwd());

/*
  |--------------------------------------------------------------------------
  | Register The Composer Auto Loader
  |--------------------------------------------------------------------------
  |
  |
 */

define('COMPOSER_DIR', WD . '/components/composer');

file_exists(COMPOSER_DIR) or die('Composer files missing, run composer install');
require_once COMPOSER_DIR . '/autoload.php';

/*
  |--------------------------------------------------------------------------
  | Setup core files
  |--------------------------------------------------------------------------
  |
  |
 */
define('CORE_DIR', COMPOSER_DIR . '/liteframe/liteframe-core/src');

file_exists(CORE_DIR) or die('Could not locate core files, composer files maybe missing. Run composer install');
$boot_file = CORE_DIR . '/bootstrap/boot.php';
require_once $boot_file;
