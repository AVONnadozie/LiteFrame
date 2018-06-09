<?php

namespace LiteFrame\CLI\Commands;

use LiteFrame\CLI\Command;
use LiteFrame\CLI\Output;
use LiteFrame\CLI\Routing\Route;
use LiteFrame\CLI\Routing\Router;

/**
 * Description of Help
 */
class Help extends Command
{

    /**
     * Command line argument names definition
     *
     * list of names for command line arguments in their respective order, separated by spaces
     * @var string
     */
    public $definition = "command";

    public function run()
    {
        $command = $this->getCommand();
        if (empty($command)) {
            $this->warn('No command specified.');
            $this->output('Try php cli <command>');
        }
        $this->output(PHP_EOL);

        $arg = $this->getArgument('command');
        if ($arg) {
            $this->printHelp($arg);
        } else {
            $this->printHelp();
        }
    }

    private function printHelp($command = null)
    {
        $routes = Router::getInstance()->getRoutes();
        if ($command) {
            if (isset($routes[$command])) {
                $this->printHelpForRoute($routes[$command]);
            } else {
                $this->error('Command not found');
            }
        } else {
            $output = [];
            foreach ($routes as $command => $route) {
                if (empty($command)) {
                    continue; //Skip other description for Help command
                }
                $output[$command] = $route->getDescription();
            }
            
            asort($output);
            $this->info('Available CLI commands:');
            foreach ($output as $command => $description) {
                $text = $this->paint($command, $description);
                $this->output($text);
            }
        }
    }
    
    private function printHelpForRoute(Route $route)
    {
        $description = $help = $route->getDescription();
        $class = $route->getClass();
        if ($class) {
            $help = $class::getHelp()?:$help;
        }
        $command = $route->getCommand();
        if (empty($description) && empty($help)) {
            $this->output("No description specified for '$command'");
        } else {
            if ($description !== $help) {
                $this->output($description);
            }

            $this->output($help);
        }
    }

    private function paint($command, $description)
    {
        return Output::green($command) . " - $description";
    }
}
