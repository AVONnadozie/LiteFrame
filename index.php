<?php

/*
  |--------------------------------------------------------------------------
  | Register files
  |--------------------------------------------------------------------------
  | This will include every file required for this application to run
 */
require_once './core/bootstrap/autoload.php';

/*
  |--------------------------------------------------------------------------
  | Take off
  |--------------------------------------------------------------------------
 */
$kernel = \LiteFrame\Kernel::getInstance();
$kernel->handleRequest();
