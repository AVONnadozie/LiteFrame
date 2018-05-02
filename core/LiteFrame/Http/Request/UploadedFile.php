<?php

namespace LiteFrame\Http\Request;

use Exception;
use LiteFrame\Storage\File;

class UploadedFile extends File
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
        if (!is_uploaded_file($key)) {
            throw new Exception("File '$key' was not received");
        }
        parent::__construct($_FILES[$this->key]['name']);
    }
    
    public function saveTo($path, $name)
    {
    }
}
