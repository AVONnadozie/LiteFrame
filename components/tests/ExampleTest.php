<?php

class ExampleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * A basic functional test example.
     */
    public function testBasicExample()
    {
        $this->assertTrue(true);
    }

    public function testNormalizePath()
    {
        $oldPath = 'path/to/file';
        $path = normalizePath($oldPath);
        $normalized = windows_os() ? 'path\\to\\file' === $path : $path == $oldPath;
        $this->assertTrue($normalized);
    }
}
