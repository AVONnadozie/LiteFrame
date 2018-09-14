<?php


use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/*
 * Allow for PHPUnit 4.* while XML_Util is still usable on PHP 5.4
 */
if (!class_exists('PHPUnit_Framework_TestCase')) {

    class PHPUnit_Framework_TestCase extends PHPUnitTestCase
    {
        
    }

}