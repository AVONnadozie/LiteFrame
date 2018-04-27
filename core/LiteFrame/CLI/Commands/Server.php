<?php


namespace LiteFrame\CLI\Commands;

use LiteFrame\CLI\Command;
use LiteFrame\CLI\Output;

/**
 * Description of Server
 */
class Server extends Command
{
    /**
     * Command line argument names definition
     *
     * list of names for command line arguments in their respective order, separated by spaces
     * @var string
     */
    public $definition = "";


    public function run()
    {
        $port = $this->getArgument('port', '500');
        $this->comment("Server started at 127.0.0.1:$port");
        $command  = "php -S 127.0.0.1:$port".(windows_os()?'':' 2>&1');
        return $this->exec($command);
    }
    
    public static function getHelp()
    {
        $help = "Syntax: php cli serve [--port]".PHP_EOL;
        $help .= PHP_EOL;
        $help .= Output::cyan("Available Options:").PHP_EOL;
        $help .= Output::green('--port'). ": server port number".PHP_EOL;
        return $help;
    }
}
