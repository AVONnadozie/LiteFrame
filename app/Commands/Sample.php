<?php


namespace Commands;

class Sample extends Command
{

    
    /**
     *
     * Command line argument names definition
     *
     * list of names for command line arguments in their respective order, separated by spaces
     * @var string
     */
    public $definition = "";
    
    
    public function run()
    {
        $this->comment('Sample Command Line Hello World App');
        $this->output("Hello World");
    }
}
