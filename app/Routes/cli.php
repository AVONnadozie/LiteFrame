<?php
/**
 * Map commands to classes here
 */

use LiteFrame\CLI\Routing\Router;

Router::map('hello', 'SampleCommand', 'Sample Hello World command');
