<?php

use LiteFrame\Testing\TestCase;
use LiteFrame\Utility\SimpleCURL;

class SimpleCURLTest extends TestCase
{

    /**
     * test the Validator email rule
     */
    public function testPOST()
    {
        //Good URL
        $response1 = SimpleCURL::post('example.com');
        $this->assertTrue(is_string($response1) !== false);
        //Bad URL
        $response2 = SimpleCURL::post('');
        $this->assertFalse(!!$response2);
    }
    
    public function testGET()
    {
        //Good URL
        $response1 = SimpleCURL::get('example.com');
        $this->assertTrue(strpos($response1, 'Example') !== false);
        //Bad URL
        $response2 = SimpleCURL::get('');
        $this->assertFalse(!!$response2);
    }

    public function testUpdateURLWithParameters()
    {
        $url = 'http://example.com/jdfh?a=?46#ghf';
        $data = ['b' => 4, 'c' => true];
        $simpleCURL = new SimpleCURL;
        $newurl = $simpleCURL->updateURLWithParameters($url, $data);
        //Check host and path
        $this->assertTrue(strpos($newurl, 'http://example.com/jdfh') === 0);
        //Check for parameters
        $this->assertTrue(strpos($newurl, 'a=' . urlencode('?46')) !== false);
        $this->assertTrue(strpos($newurl, 'b=4') !== false);
        $this->assertTrue(strpos($newurl, 'c=1') !== false);
    }
}
