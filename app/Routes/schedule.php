<?php
/**
 * Schedule commands here
 */

use LiteFrame\CLI\Scheduler;

/* @var $scheduler Scheduler */
/* @var \LiteFrame\CLI\Scheduler $scheduler */

$scheduler->command('hello')->everyMinute()->output('components/logs/cron.log');
