<?php

namespace LiteFrame\Storage;

class File
{
    protected $absolutePath;
    protected $relativePath;

    /**
     * Create file object
     * @param type $path relative path to project directory
     */
    public function __construct($path)
    {
        $this->absolutePath = basePath($path);
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
        $dir = $this->absolutePath;
        if (is_file($dir)) {
            $name = basename($dir);
            $dir = rtrim($dir, $name);
        }

        if (file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
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

    /**
     * Delete file from storage
     */
    public function delete() {
        if (file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
            return true;
        }
        return false;
    }

    /**
     * Gets last access time of file
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the time the file was last accessed, or <b>FALSE</b> on failure.
     * The time is returned as a Unix timestamp.
     */
    public function lastAccessTime() {
        return fileatime($this->getAbsolutePath());
    }

    /**
     * Gets file group
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the group ID of the file, or <b>FALSE</b> if
     * an error occurs. The group ID is returned in numerical format, use
     * <b>posix_getgrgid</b> to resolve it to a group name.
     * Upon failure, <b>FALSE</b> is returned.
     */
    public function inodeChangeTime() {
        return filectime($this->getAbsolutePath());
    }

    /**
     * Gets file group
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the group ID of the file, or <b>FALSE</b> if
     * an error occurs. The group ID is returned in numerical format, use
     * <b>posix_getgrgid</b> to resolve it to a group name.
     * Upon failure, <b>FALSE</b> is returned.
     */
    public function group() {
        return filegroup($this->getAbsolutePath());
    }

    /**
     * Gets file inode
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the inode number of the file, or <b>FALSE</b> on failure.
     */
    public function inode() {
        return fileinode($this->getAbsolutePath());
    }

    /**
     * Gets file modification time
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the time the file was last modified, or <b>FALSE</b> on failure.
     * The time is returned as a Unix timestamp, which is
     * suitable for the <b>date</b> function.
     */
    public function modificationTime() {
        return filemtime($this->getAbsolutePath());
    }

    /**
     * Gets file owner
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the user ID of the owner of the file, or <b>FALSE</b> on failure.
     * The user ID is returned in numerical format, use
     * <b>posix_getpwuid</b> to resolve it to a username.
     */
    public function owner() {
        return fileowner($this->getAbsolutePath());
    }

    /**
     * Gets file permissions
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the file's permissions as a numeric mode. Lower bits of this mode
     * are the same as the permissions expected by <b>chmod</b>,
     * however on most platforms the return value will also include information on
     * the type of file given as <i>filename</i>. The examples
     * below demonstrate how to test the return value for specific permissions and
     * file types on POSIX systems, including Linux and Mac OS X.
     * </p>
     * <p>
     * For local files, the specific return value is that of the
     * st_mode member of the structure returned by the C
     * library's <b>stat</b> function. Exactly which bits are set
     * can vary from platform to platform, and looking up your specific platform's
     * documentation is recommended if parsing the non-permission bits of the
     * return value is required.
     */
    public function permissions() {
        return fileperms($this->getAbsolutePath());
    }

    /**
     * Gets file size
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return int the size of the file in bytes, or <b>FALSE</b> (and generates an error
     * of level <b>E_WARNING</b>) in case of an error.
     */
    public function size() {
        return filesize($this->getAbsolutePath());
    }

    /**
     * Gets file type
     * 
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return string the type of the file. Possible values are fifo, char,
     * dir, block, link, file, socket and unknown.
     * </p>
     * <p>
     * Returns <b>FALSE</b> if an error occurs. <b>filetype</b> will also
     * produce an <b>E_NOTICE</b> message if the stat call fails
     * or if the file type is unknown.
     */
    public function type() {
        return filetype($this->getAbsolutePath());
    }

}
