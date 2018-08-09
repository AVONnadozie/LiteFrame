<?php

namespace LiteFrame\Storage;

class File
{
    protected $absolutePath;
    protected $relativePath;

    public function __construct($path)
    {
        $this->absolutePath = realpath($path);
        $this->relativePath = $this->resolveRelativePath($path);
    }

    private function resolveRelativePath($path) {
        $base = basePath();
        if (stripos($path, $path) === 0) {
            return str_replace($base, '', $path);
        }
        return $path;
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

    /**
     * Get path relative to the storage folder
     * @return type
     */
    public function getStoragePath() {
        $storage_path = config('app.storage', 'storage');
        return preg_replace("/^\/?$storage_path/", '', $this->getRelativePath());
    }

    /**
     * Get absolute path of file
     * @return type
     */
    public function getAbsolutePath() {
        return $this->absolutePath;
    }

    /**
     * Get path relative to the project working directory
     * @return type
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function setAbsolutePath($absolutePath)
    {
        $this->absolutePath = $absolutePath;
        return $this;
    }

    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;
        return $this;
    }
}
