<?php

namespace LiteFrame\Storage;

class File
{
    protected $path;
    
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    public function ext()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }
}
