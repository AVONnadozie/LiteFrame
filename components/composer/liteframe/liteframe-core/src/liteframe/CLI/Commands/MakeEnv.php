<?php

namespace LiteFrame\CLI\Commands;

use LiteFrame\CLI\Command;
use LiteFrame\CLI\Output;

/**
 * Description of Server
 */
class MakeEnv extends Command
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
        if (file_exists('components/env.php')) {
            Output::warn('env file already exists.');
        } else {
            if (copy('components/env.sample', 'components/env.php')) {
                Output::success('env file created succesfully');
            } else {
                Output::error('creation of env file failed.');
            }
        }
    }
}
