<?php

/*
  |--------------------------------------------------------------------------
  | Bootstrap Application
  |--------------------------------------------------------------------------
  |
  |
 */

$autoload_files = __DIR__ . '/components/bootstrap/autoload.php';
require_once $autoload_files;

/*
  |--------------------------------------------------------------------------
  | Take off
  |--------------------------------------------------------------------------
 */
$kernel = \LiteFrame\Kernel::getInstance();
$kernel->handleRequest();
