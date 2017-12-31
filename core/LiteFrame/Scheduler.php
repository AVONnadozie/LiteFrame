<?php


namespace LiteFrame;

/**
 * Description of Scheduler
 *
 * @author Victor Anuebunwa
 */
class Scheduler extends \GO\Scheduler
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
        $fn = function() use ($command) {
            
        };

        return $this->call($fn, $args, $id);
    }

}
