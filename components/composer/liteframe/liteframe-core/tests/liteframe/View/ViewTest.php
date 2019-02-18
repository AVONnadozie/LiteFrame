<?php

use LiteFrame\View\View;

class ViewTest extends \LiteFrame\Testing\TestCase
{
    const TEST_TEMPLATES = __DIR__ . '/templates';

    public function testDefaultViews()
    {
        $errorBag = new \LiteFrame\Exception\ErrorBag(404);
        $data = ['bag' => $errorBag, 'errorBag' => $errorBag];
        $content = View::fetch('errors/404', $data);

        $this->assertNotEmpty($content);
    }

    public function testView()
    {
        $content = View::fetch('base', [], self::TEST_TEMPLATES);
        $this->assertEquals('works!', $content);
    }

    public function testFailure()
    {
        $this->expectException(Exception::class);
        View::fetch('fake.path');
    }

    public function testPriority()
    {
        $content = View::fetch('overideme', [], self::TEST_TEMPLATES);
        //Content from overideme.blade.php expected instead of overideme.php
        $this->assertEquals('override works!', $content);
    }
//
//    public function testLogin(){
//        View::loginUser('admin','admin');
//        $content = View::fetch('overideme', [], self::TEST_TEMPLATES);
//        $this->assertEqualsIgnoringWhitespace('override works! Login too!', $content);
//    }
//
//    public function assertEqualsIgnoringWhitespace($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false) {
//        $this->assertEquals(
//            preg_replace('/\s/', '', $expected),
//            preg_replace('/\s/', '', $actual),
//            $message, $delta, $maxDepth, $canonicalize, $ignoreCase
//        );
//    }
}