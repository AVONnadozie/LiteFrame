<?php

namespace LiteFrame\View;

use Exception;
use LiteFrame\Exception\ErrorBag;

class View
 {

    /**
     * Fetch content of a view
     * @param string $view View file
     * @param array $data Data to be passed to view
     * @return string
     */
    public static function fetch($view, $data = []) {
        $file = static::getFilePath($view);

        return static::getContent($file, $data);
    }

    /**
     * Get full path to view file
     * @param string $view View file
     * @return string|null
     */
    public static function getFilePath($view) {
        $paths = config('view.path');
        if (empty($paths)) {
            $paths = [appPath('Views')];
        }

        $filename = trim($view, '/') . '.php';
        $file = null;
        foreach ($paths as $dir) {
            $file = nPath($dir, $filename);
            if (file_exists($file)) {
                break;
            }
        }
        return $file;
    }

    /**
     * Return error page content for the given ErrorBag
     * @param ErrorBag $errorBag
     * @return string
     */
    public static function getErrorPage(ErrorBag $errorBag) {
        $code = $errorBag->getCode();
        //Fetch user error page for the error code
        $file = static::getFilePath("errors/$code");

        //If page does not exist, fetch default error page
        if (!file_exists($file)) {
            $file = static::getFilePath("errors/default");

            //We are left with no choose but to use our default error pages
            if (!file_exists($file)) {
                $file = _corePath("html/errors/$code.php");
                if (!file_exists($file)) {
                    $file = _corePath('html/errors/default.php');
                }
            }
        }

        return static::getContent($file, ['bag' => $errorBag, 'errorBag' => $errorBag]);
    }

    /**
     * Get view content for the given file 
     * @param string $file absolute path to file
     * @param array $data data
     * @return string|false
     * @throws Exception
     */
    private static function getContent($file, $data = []) {
        $path = fixPath($file);
        $content = false;
        if (file_exists($path)) {
            //Set data
            foreach ($data as $key => $value) {
                $$key = $value;
            }

            ob_start();
            require $path;
            $content = ob_get_clean();
        } else {
            throw new Exception("View $path not found");
        }

        return $content;
    }

    /**
     * Check if view file exists
     * @param string $view View file
     * @return boolean
     */
    public static function exists($view) {
        return file_exists(static::getFilePath($view));
    }

}
