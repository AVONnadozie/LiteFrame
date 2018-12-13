<?php

namespace LiteFrame\Http\Request;

use Exception;
use LiteFrame\Storage\File;
use function nPath;
use function request;
use function storagePath;

class UploadedFile
{
    protected $name;
    protected $uploadInfo;

    public function __construct($name)
    {
        $this->name = $name;
        $this->validate();
        $this->uploadInfo = $_FILES[$this->name];
    }

    /**
     * Save uploaded file publicly to the path
     * @param type $path
     * @param type $name
     * @return type
     */
    public function savePublicly($path = '', $name = '')
    {
        return $this->save($path, $name, true);
    }

    /**
     *
     * @param type $path
     * @param type $name
     * @return File
     */
    public function save($path = '', $name = '', $public = false)
    {
        $folder = $public ? 'public' : 'private';
        if ($path) {
            if (!file_exists(storagePath($path))) {
                mkdir(storagePath($path), 0777, true);
            }
            $savePath = storagePath(nPath($path, $name), $folder);
        } else {
            $savePath = storagePath($this->uploadInfo['name'], $folder);
        }
        $status = move_uploaded_file($this->uploadInfo['tmp_name'], $savePath);
        if ($status) {
            return new File($savePath);
        } else {
            return $status;
        }
    }
    
    private function validate()
    {
        if (!request()->hasFile($this->name)) {
            throw new Exception("File '$this->name' was not received");
        }
        
        if ($_FILES[$this->name]['error'] != UPLOAD_ERR_OK) {
            $message = $this->getUploadErrorMessage($_FILES[$this->name]['error']);
            throw new Exception("File upload failed: $message");
        }
        //Check mime type, file size, etc.
    }

    public function ext()
    {
        $dots = explode('.', $this->uploadInfo['name']);
        return array_pop($dots);
    }

    private function getUploadErrorMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_CANT_WRITE:
                return 'Write access denied';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload process stopped by extention';
            case UPLOAD_ERR_FORM_SIZE:
                return 'Uploaded file is larger than form file size';
            case UPLOAD_ERR_INI_SIZE:
                return 'Uploaded file is too large';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'We are missing a temporary folder.';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded.';
            default:
                return 'Unknown upload error.';
        }
    }
}
