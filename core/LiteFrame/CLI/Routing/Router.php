<?php

namespace LiteFrame\CLI\Routing;

use Exception;
use LiteFrame\CLI\Args;
use LiteFrame\CLI\Command;
use LiteFrame\CLI\Scheduler;

class Router
{
    protected static $instance;
    protected $routes = [];
    protected $args;

    /**
     * Return singleton class instance.
     *
     * @return Router
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
            //load cli commands routes
            self::$instance->loadRoutes();
        }

        return self::$instance;
    }
    
    /**
     * Add core commands to routes
     */
    private function addCoreCommands()
    {
        $this->addCommand('serve', \LiteFrame\CLI\Commands\Server::class, 'Start PHP Server');
        $this->addCommand('schedule', \LiteFrame\CLI\Commands\Schedule::class, 'Schedule cron jobs');
        $this->addCommand('help', \LiteFrame\CLI\Commands\Help::class, 'Display command descriptions');
        //Display help when no command is specified
        $this->addCommand('', \LiteFrame\CLI\Commands\Help::class);
    }

    public static function map($command, $target, $description = '')
    {
        $self = self::getInstance();
        //Restrict users to Commands namespace
        $class = "\\Commands\\{$target}";
        $self->addCommand($command, $class, $description);
    }
    
    private function addCommand($command, $class, $description = '')
    {
        $self = self::getInstance();
        if (isset($self->routes[$command])) {
            throw new Exception("Command '$command' already mapped.");
        }

        $self->routes[$command] = new Route($command, $class, $description);
    }

    /**
     * Loads CLI commands and return matched target
     * @param type $command command to match
     * @return Scheduler
     * @throws Exception
     */
    public function matchCommand(Args $args)
    {
        $this->args = $args;
        return $this->getTarget($args->getCommand());
    }

    private function loadRoutes()
    {
        if (empty($this->routes)) {
            $this->addCoreCommands();

            $routeFile = base_path('app/Routes/cli.php');

            if (file_exists($routeFile)) {
                require_once $routeFile;
            } else {
                throw new Exception("Route definition file $routeFile does not exist");
            }
        }
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getRoute($command)
    {
        if (!isset($this->routes[$command])) {
            return null;
        }
        
        return $this->routes[$command];
    }

    /**
     * Return target for the specified command, note that this method only searches the
     * @param type $command
     * @return Command
     * @throws Exception
     */
    public function getTarget($command)
    {
        if (empty($this->routes[$command])) {
            return null;
        }

        /* @var $route Route */
        $route = $this->routes[$command];
        return $route->getTarget($this->args);
    }

    public function getDescription($command)
    {
        if (!isset($this->routes[$command])) {
            return null;
        }
        
        /* @var $route Route */
        $route = $this->routes[$command];
        return $route->getDescription();
    }
}
