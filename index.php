<?php

/*
  |--------------------------------------------------------------------------
  | Bootstrap Application
  |--------------------------------------------------------------------------
  |
  |
 */

$composer_files = __DIR__ . '/components/bootstrap/autoload.php';
require_once $composer_files;

/*
  |--------------------------------------------------------------------------
  | Take off
  |--------------------------------------------------------------------------
 */
$kernel = \LiteFrame\Kernel::getInstance();
$kernel->handleRequest();
