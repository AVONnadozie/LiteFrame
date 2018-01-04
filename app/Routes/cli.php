<?php

use LiteFrame\CLI\Scheduler;

/* @var $scheduler Scheduler */
/* @var \LiteFrame\CLI\Scheduler $scheduler */

$scheduler->command('Sample@greet')->output('components/logs/cron.log');
