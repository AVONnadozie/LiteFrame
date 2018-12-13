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

$composer_files = realpath(__DIR__ . '/../../vendor/autoload.php');
file_exists($composer_files) or die('Composer files missing, run composer install');
require_once $composer_files;

/*
  |--------------------------------------------------------------------------
  | Setup core files
  |--------------------------------------------------------------------------
  |
  |
 */
define('CORE_DIR', realpath(__DIR__ . '/../'));
file_exists(CORE_DIR) or die('Could not locate core files, composer files maybe missing. Run composer install');

$boot_file = CORE_DIR . '/bootstrap/boot.php';
require_once $boot_file;
