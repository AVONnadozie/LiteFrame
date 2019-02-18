<?php

namespace LiteFrame\View;

use eftec\bladeone\BladeOne;
use Exception;

class View
{
    private $paths = [];
    private static $auth = ['user' => null, 'role' => null];
    private $blade;

    /**
     * View constructor.
     * @param array|string $paths Path(s) to find view files
     */
    function __construct($paths = [])
    {
        $this->setPaths($paths);
    }

    /**
     * Set view paths
     * @param array $paths Path(s) to find view files
     * @return View
     */
    public function setPaths($paths)
    {
        $paths = (array)$paths;
        //Append framework's view path
        $paths[] = realpath(__DIR__ . '/../../views');

        $this->paths = $paths;
        return $this;
    }

    /**
     * Get view paths
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Fetch content of a view
     * @param string $name The view file name relative to the view path(s). Dot (.) or (/) can be use as directory separator
     * @param array $data Data to be passed to view
     * @param array $paths Paths to find view files
     * @return string
     */
    public static function fetch($name, $data = [], $paths = [])
    {
        if (empty($paths)) {
            $paths = config('view.path');
            if (empty($paths)) {
                $paths = [appPath('Views')];
            }
        }

        $view = new static($paths);

        //Check if it's blade
        $bladeFile = $view->getFilePath($name, '.blade.php');
        if ($bladeFile) {
            return $view->readBladeFile($name, $data);
        } else {
            //else, read file old school way
            $file = $view->getFilePath($name);
            return $view->getContent($file, $data);
        }
    }

    /**
     * Read the content of a blade template file
     * @param $name
     * @param array $data
     * @return string
     * @throws Exception
     */
    private function readBladeFile($name, $data = [])
    {
        return $this->getBladeInstance()
            ->run(pathToDot($name), $data);
    }

    public function getBladeInstance()
    {
        if (!$this->blade) {
            $cache = storagePath('system/views');
            if (appIsOnDebugMode()) {
                $mode = BladeOne::MODE_DEBUG;
            } else if (appIsLocal()) {
                $mode = BladeOne::MODE_SLOW;
            } else {
                $mode = BladeOne::MODE_AUTO;
            }

            $this->blade = new BladeOne($this->paths, $cache, $mode);
            if (static::$auth['user']) {
                $this->blade->login(static::$auth['user'], static::$auth['role']);
            }

            if(!isCLI()){
                $this->blade->setBaseUrl(asset());
            }
        }

        return $this->blade;
    }

    /**
     * Sets a user for the view, this is only available when using blade
     * @param $user
     * @param null $role
     */
//    public static function loginUser($user, $role = null)
//    {
//        static::$auth['user'] = $user;
//        static::$auth['role'] = $role;
//    }


    /**
     * Get full path to view file
     * @param string $name View file name
     * @param string $extension
     * @return string|null
     */
    public function getFilePath($name, $extension = '.php')
    {
        $filename = trim(dotToPath($name), '/') . $extension;
        $file = null;
        foreach ($this->paths as $dir) {
            $realName = nPath($dir, $filename);
            if (file_exists($realName)) {
                $file = $realName;
                break;
            }
        }
        return $file;
    }

    /**
     * Get view content for the given file
     * @param string $file absolute path to file
     * @param array $data data
     * @return string|false
     * @throws Exception
     */
    private function getContent($file, $data = [])
    {
        $path = fixPath($file);
        if (file_exists($path)) {
            ob_start();
            //Extract variables
            extract($data);

            /** @noinspection PhpIncludeInspection */
            require $path;

            return ob_get_clean();
        } else {
            throw new Exception("View $path not found");
        }
    }

    /**
     * Check if view file exists
     * @param string $view View file
     * @return boolean
     */
    public function exists($view)
    {
        return file_exists($this->getFilePath($view));
    }
}
