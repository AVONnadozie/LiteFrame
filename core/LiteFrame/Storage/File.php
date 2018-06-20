<?php

namespace LiteFrame\Storage;

class File
{
    protected $absolutePath;
    protected $relativePath;
    public function __construct($path)
    {
        $this->relativePath = $path;
        $this->absolutePath = basePath($path);
    }
    
    public function ext()
    {
        return pathinfo($this->absolutePath, PATHINFO_EXTENSION);
    }
    
    public function makeDirectory()
    {
//        if is file
//        remove base name to get folder
//        call mkdir on folder
//
    }
    
    public function name()
    {
        return basename($this->absolutePath);
    }
    
    public function getMimeType()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $this->absolutePath);
        finfo_close($finfo);
        return $mime_type;
    }
}
