<?php

namespace LiteFrame\CLI\Routing;

use Exception;
use LiteFrame\CLI\Args;
use LiteFrame\CLI\Command;

class Route
{
    private $class;
    private $command;
    private $target;
    private $description;
    private $lock = false;

    public function __construct($command, $class, $description = '')
    {
        $this->setCommand($command);
        $this->setClass($class);
        $this->setDescription($description);
    }

    /**
     * Get command class
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get command
     * @return type
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Get command object if initialized
     * @return Command
     */
    public function getTarget(Args $args)
    {
        if (!is_object($this->target) && $this->getClass()) {
            $class = $this->getClass();
            $targetObject = new $class($args);
            if (!$targetObject instanceof Command) {
                $command = $this->getCommand();
                throw new Exception("$command: $class is not an instance of LiteFrame\CLI\Command");
            }
            
            $this->setTarget($targetObject);
        }

        return $this->target;
    }

    /**
     * Get command description
     * @return type
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setCommand($routeURI)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }
        
        $this->command = $routeURI;
     
        return $this;
    }

    public function setTarget($target)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }
        $this->target = $target;
        
        $this->lock = true; //Can only be set once

        return $this;
    }

    public function setDescription($description)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->description = $description;

        return $this;
    }

    public function setClass($class)
    {
        if ($this->lock) {
            throw new Exception('Route cannot be modified');
        }

        $this->class = $class;

        return $this;
    }

    /**
     * Disallow further modification to this object.
     */
    public function lock()
    {
        $this->lock = true;
    }
}
