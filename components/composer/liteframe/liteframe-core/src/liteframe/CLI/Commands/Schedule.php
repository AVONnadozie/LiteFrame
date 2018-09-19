<?php


namespace LiteFrame\CLI\Commands;

use LiteFrame\CLI\Command;
use LiteFrame\CLI\Scheduler;

/**
 * Description of Server
 */
class Schedule extends Command
{
    /**
     * Command name definition
     * @var string
     */
    public $definition = "";


    public function run()
    {
        //Set scheduler object for routes
        $scheduler = new Scheduler;
        //load routes
        $routeFile = basePath('app/Routes/schedule.php');
        require_once $routeFile;
        //Run scheduler
        $scheduler->run();
    }
}
