<?php

namespace LiteFrame\CLI;

class Output
{
    const ERROR = 0;
    const NORMAL = 1;
    const INFO = 2;
    const WARNING = 3;

    private static $colors = array(
        /**
         * Foregrounds
         */
        'black' => '0;30', 'dark_gray' => '1;30',
        'blue' => '0;34', 'light_blue' => '1;34',
        'green' => '0;32', 'light_green' => '1;32',
        'cyan' => '0;36', 'light_cyan' => '1;36',
        'red' => '0;31', 'light_red' => '1;31',
        'purple' => '0;35', 'light_purple' => '1;35',
        'brown' => '0;33', 'yellow' => '1;33',
        'light_gray' => '0;37', 'white' => '1;37',
        'normal' => '0;39',
        /**
         * Backgrounds
         */
        'b_black' => '40', 'b_red' => '41',
        'b_green' => '42', 'b_yellow' => '43',
        'b_blue' => '44', 'b_magenta' => '45',
        'b_cyan' => '46', 'b_light_gray' => '47',
    );
    
    private static $options = array(
        'bold' => '1', 'dim' => '2',
        'underline' => '4', 'blink' => '5',
        'reverse' => '7', 'hidden' => '8',
    );

    public static function write($data, $level = self::NORMAL, $newline = true)
    {
        $array = (array) $data;
        foreach ($array as $value) {
            echo(self::label($value . ($newline ? PHP_EOL : ''), $level));
        }
    }

    public static function error($data)
    {
        self::write($data, self::ERROR);
    }
    
    public static function warn($data)
    {
        self::write($data, self::WARNING);
    }
    
    public static function info($data)
    {
        self::write($data, self::INFO);
    }

    private static function label($value, $level)
    {
        switch ($level) {
            case self::INFO:
                $value = "\033[" . self::$colors['blue'] . "m" . $value . "\033[0m";
                break;
            case self::ERROR:
                $value = "\033[" . self::$colors['red'] . "m" . $value . "\033[0m";
                break;
            case self::WARNING:
                $value = "\033[" . self::$colors['yellow'] . "m" . $value . "\033[0m";
                break;
            default:
                break;
        }
        return $value;
    }

    /**
     * Get beep sound in console (if available)
     * @param  integer $count Beep count
     * @return string         Beep string
     */
    public static function beep($count = 1, $return = false)
    {
        $beep = str_repeat("\007", $count);
        if ($return) {
            return $beep;
        } else {
            echo $beep;
        }
    }

    /**
     * @param  string $option Text Color
     * @param  array  $args  Options (Background color and other options)
     * @return string Colored string
     */
    public static function __callStatic($option, $args)
    {
        //Get string
        $string = $args[0];
        array_shift($args);
        //Apply options
        array_push($args, $option);
        return self::setOptions($string, $args);
    }
    
    private static function setOptions($string, array $options)
    {
        $modded = '';
        foreach ($options as $option) {
            //Find color
            if (isset(self::$colors[$option])) {
                $modded .= "\033[" . self::$colors[$option] . "m";
            }
            //Find option
            elseif (isset(self::$options[$option])) {
                $modded .= "\033[" . self::$options[$option] . "m";
            }
        }
        $modded .= $string . "\033[0m";
        return $modded;
    }
}
