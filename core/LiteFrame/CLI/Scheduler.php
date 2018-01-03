<?php


namespace LiteFrame\CLI;

use DateTime;
use GO\Job;
use GO\Scheduler as GoScheduler;

class Scheduler extends GoScheduler
{


    /**
     * Queues a command for execution.
     *
     * @param  string  $command  The command to execute
     * @param  array  $args  Optional arguments to pass to the php script
     * @param  string  $id   Optional custom identifier
     * @return Job
     */
    public function command($command, $args = [], $id = null)
    {
        list($class, $method) = getClassAndMethodFromString($command);
        $fn = function() use ($class, $method) {
            $commmandClass = "\\Commands\\$class";
            $class = new $commmandClass;
            return $class->$method();
        };

        return $this->call($fn, $args, $id);
    }

    public function run(DateTime $runTime = null)
    {

        return parent::run($runTime);
    }

}
