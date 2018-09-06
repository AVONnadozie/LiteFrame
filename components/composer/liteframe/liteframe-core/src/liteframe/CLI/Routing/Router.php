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

    private function __construct()
    {
    }

    /**
     * Return singleton class instance.
     *
     * @return Router
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
            //load cli commands routes
            static::$instance->loadRoutes();
        }

        return static::$instance;
    }
    
    public static function map($command, $target, $description = '')
    {
        $self = static::getInstance();
        //Restrict users to Commands namespace
        $class = "\\Commands\\{$target}";
        $self->addCommand($command, $class, $description);
    }
    
    private function addCommand($command, $class, $description = '')
    {
        $self = static::getInstance();
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
            require_once $this->coreRouteFile();
            $routeFile = $this->userRouteFile();
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

    /**
     * Get Route
     * @param type $command
     * @return Route
     */
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
    
    protected function userRouteFile()
    {
        return basePath('app/Routes/cli.php');
    }
    
    protected function coreRouteFile()
    {
        return __DIR__.'/routes.php';
    }
}
