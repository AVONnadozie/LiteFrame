<?php

namespace LiteFrame\Storage;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use function basePath;
use function config;

class File extends SymfonyFile
{
    protected $absolutePath;
    protected $relativePath;

    /**
     * Create file object
     * @param type $path relative path to project directory
     */
    public function __construct($path, $checkPath = true)
    {
        parent::__construct($path, $checkPath);
        $this->absolutePath = $this->getPathname();
        $this->relativePath = $this->resolveRelativePath($this->absolutePath);
    }

    private function resolveRelativePath($path)
    {
        if ($path) {
            $base = basePath();
            if (stripos($path, $path) === 0) {
                return str_replace($base, '', $path);
            }
            return $path;
        }
        return null;
    }

    public function getExtention()
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

    /**
     * Get path relative to the storage folder
     * @return type
     */
    public function getStoragePath()
    {
        $storage_path = config('app.storage', 'storage');
        return preg_replace("/^\/?$storage_path/", '', $this->getRelativePath());
    }

    /**
     * Get absolute path of file
     * @return type
     */
    public function getAbsolutePath()
    {
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
    public function delete()
    {
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
    public function getLastAccessTime()
    {
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
    public function getInodeChangeTime()
    {
        return filectime($this->getAbsolutePath());
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
    public function getModificationTime()
    {
        return filemtime($this->getAbsolutePath());
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
    public function getPermissions()
    {
        return fileperms($this->getAbsolutePath());
    }
}
