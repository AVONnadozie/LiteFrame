<?php


namespace LiteFrame\CLI;

use DateTime;
use GO\Job;
use GO\Scheduler as GoScheduler;

class Scheduler extends GoScheduler implements Runnable
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
        $target = Router::getInstance()->getRoute($command);
        $fn = function () use ($target) {
            return $target->run();
        };

        return $this->call($fn, $args, $id);
//        return $this->call("cli $target", $args, $id);
    }

    public function run(DateTime $runTime = null)
    {
        return parent::run($runTime);
    }
}
