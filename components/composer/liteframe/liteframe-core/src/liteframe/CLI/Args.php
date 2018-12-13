<?php

namespace LiteFrame\CLI;

/**
 * Command Line Interface (CLI) utility trait for parsing command line arguments
 *
 * This command line option parser supports any combination of three types
 * of options (switches, flags and arguments) and returns a simple array.
 *
 * php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
 * > 'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
 *   [0]       => "plain-arg"
 *   ["foo"]   => true
 *   ["bar"]   => "baz"
 *   ["funny"] => "spam=eggs"
 *   ["also-funny"]=> "spam=eggs"
 *   [1]       => "plain arg 2"
 *   ["a"]     => true
 *   ["b"]     => true
 *   ["c"]     => true
 *   ["k"]     => "value"
 *   [2]       => "plain arg 3"
 *   ["s"]     => "overwrite"
 *
 * @author              Victor Anuebunwa <hello@victoranuebunwa.com>
 * @see                 https://gist.github.com/jadb/3949954
 * @usage               $args = Args::parse();
 */
class Args
{
    protected $args;
    
    protected function __construct()
    {
    }

    /**
     * Route definition
     * @var string
     */
    protected $map = '';
    protected $command = '';
    protected $file = '';
    protected static $booleanMap = array(
        'y' => true,
        'n' => false,
        'yes' => true,
        'no' => false,
        'true' => true,
        'false' => false,
        '1' => true,
        '0' => false,
        'on' => true,
        'off' => false,
    );

    public static function parse()
    {
        global $argv;
        $file = array_shift($argv);
        $command = array_shift($argv);
        $out = array();
        foreach ($argv as $arg) {
            //escape
//            $arg = escapeshellarg($arg);
            // --foo --bar=baz
            if (substr($arg, 0, 2) == '--') {
                $eqPos = strpos($arg, '=');
                // --foo
                if ($eqPos === false) {
                    $key = substr($arg, 2);
                    $value = isset($out[$key]) ? $out[$key] : true;
                    $out[$key] = $value;
                }
                // --bar=baz
                else {
                    $key = substr($arg, 2, $eqPos - 2);
                    $value = substr($arg, $eqPos + 1);
                    $out[$key] = $value;
                }
            }
            // -k=value -abc
            elseif (substr($arg, 0, 1) == '-') {
                // -k=value
                if (substr($arg, 2, 1) == '=') {
                    $key = substr($arg, 1, 1);
                    $value = substr($arg, 3);
                    $out[$key] = $value;
                }
                // -abc
                else {
                    $chars = str_split(substr($arg, 1));
                    foreach ($chars as $char) {
                        $key = $char;
                        $value = isset($out[$key]) ? $out[$key] : true;
                        $out[$key] = $value;
                    }
                }
            }
            // plain-arg
            else {
                $value = $arg;
                $out[] = $value;
            }
        }

        $self = new static;
        $self->file = $file;
        $self->command = $command;
        $self->args = $out;
        return $self;
    }

    /**
     * Get option/boolean
     */
    public function getOption($key, $default = false)
    {
        if (!isset($this->args[$key])) {
            return $default;
        }
        $value = $this->args[$key];
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return (bool) $value;
        }
        if (is_string($value)) {
            $value = strtolower($value);
            if (static::booleanValue($value)) {
                return static::booleanValue($value);
            }
        }
        return $default;
    }

    /**
     * Map command line arguments to keys
     * @param type $definition
     */
    public function map($definition)
    {
        $fixed = preg_replace('/\s+/', ' ', $definition);
        if (empty($fixed)) {
            return;
        }
        
        $names = explode(' ', $fixed);
        $newArgs = [];
        foreach ($this->args as $key => $value) {
            if (!is_int($key) && in_array($key, $names)) {
                throw new \Exception("Duplicate key {$key}");
            }

            if (is_int($key) && isset($names[$key])) {
                //Map command
                $newArgs[$names[$key]] = $value;
            } else {
                //Ignore options
                $newArgs[$key] = $value;
            }
        }
        $this->args = $newArgs;
    }

    public function getArgument($key, $default = null)
    {
        return isset($this->args[$key]) ? $this->args[$key] : $default;
    }

    public function getArguments()
    {
        return $this->args;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public static function booleanValue($value) {
        $key = strtolower($value);
        if (isset(static::$booleanMap[$key])) {
            return static::$booleanMap[$key];
        }
    }

}
