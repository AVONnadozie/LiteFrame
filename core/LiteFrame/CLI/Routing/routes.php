<?php

/*
 * Core commands
 */


$this->addCommand('make:env', \LiteFrame\CLI\Commands\MakeEnv::class, 'Create application env file if it does not exist');
$this->addCommand('serve', \LiteFrame\CLI\Commands\Server::class, 'Start PHP Server');
$this->addCommand('schedule', \LiteFrame\CLI\Commands\Schedule::class, 'Schedule cron jobs');
$this->addCommand('help', \LiteFrame\CLI\Commands\Help::class, 'Display command descriptions');
//Display help when no command is specified
$this->addCommand('', \LiteFrame\CLI\Commands\Help::class);
