<?php

use LiteFrame\Testing\TestCase;

class SystemTest extends TestCase
{
    public function testDotToPath()
    {
        $string = dotToPath('fake.file.path');
        $this->assertEquals('fake/file/path', $string);
    }

    public function testPathToDot()
    {
        $string = pathToDot('fake/file\path');
        $this->assertEquals('fake.file.path', $string);
    }

    public function testNPath()
    {
        $path = '/to/file.blade.php';
        $context = '/fake/path';
        $nPath = nPath($context,$path);
        $this->assertEquals('/fake/path/to/file.blade.php', $nPath);
    }

    public function testFixPath()
    {
        $path = 'path\to/file.blade.php';
        $fPath = fixPath($path);
        $this->assertEquals('path'.DS.'to'.DS.'file.blade.php', $fPath);

    }

    public function testFixURL()
    {
        $URL = 'http://url\to/file.com';
        $fURL = fixPath($URL);
        $this->assertEquals('http://url/to/file.com', $fURL);

    }
}
