<?php

namespace LiteFrame\CLI;

abstract class Command implements Runnable
{

    /**
     * Command line argument names definition
     *
     * list of names for command line arguments in their respective order, separated by spaces
     * @var string
     */
    public $definition = "";

    /* @var $args Args */
    protected $args;

    public function __construct(Args $args)
    {
        $this->args = $args;
        //Map parameter names
        $this->args->map($this->definition);
    }

    public function getOption($key, $default)
    {
        return $this->args->getOption($key, $default);
    }

    public function getArgument($key, $default = null)
    {
        return $this->args->getArgument($key, $default);
    }

    public function getCommand()
    {
        return $this->args->getCommand();
    }

    public function ask($question, $trim = true)
    {
        echo "$question: ";
        if (windows_os()) {
            $input = rtrim(fgets(STDIN), "\n");
        } else {
            $input = readline();
        }
        return $input;
    }

    public function confirm($question)
    {
        do {
            $q = $question ?: 'Are you sure?';
            $answer = $this->ask("$q [y/n]");
            $key = trim($answer);
        } while (!isset(Args::$booleanMap[$key])); //Repeat while answer is not a valid yes/no response
        return $key;
    }

    public function comment($text)
    {
        Output::write(Output::yellow($text));
    }

    public function output($text, $newline = true)
    {
        Output::write($text, Output::NORMAL, $newline);
    }
    
    public function error($text)
    {
        Output::error($text);
    }
    
    public function warn($text)
    {
        Output::warn($text);
    }
    
    public function info($text)
    {
        Output::info($text);
    }
    
    public function beep($count = 1, $return = false)
    {
        Output::beep($count, $return);
    }

    /**
     * More intelligent interface to system calls
     *
     * @param $cmd
     * @param string $write
     *
     * @return array
     */
    public function exec($cmd, $write = '', $keepalive = false)
    {
        if ($keepalive) {
            return system($cmd);
        } else {
            $cmd = escapeshellcmd($cmd);
            $process = proc_open($cmd, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']], $pipes);

            if (!is_resource($process)) {
                return ['Could not open resource stream', 1];
            }

            fwrite($pipes[0], $write);
            fclose($pipes[0]);


            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $rtn = proc_close($process);

            return [$rtn ? $stderr : $stdout, $rtn];
        }
    }
    
    /**
     * Command help
     * @return string
     */
    public static function getHelp()
    {
        return '';
    }
}
