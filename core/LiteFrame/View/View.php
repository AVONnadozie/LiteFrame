<?php

namespace LiteFrame\View;

use Exception;
use LiteFrame\Exception\ErrorBag;

class View
{
    public function fetch($path, $data = [])
    {
        $file = $this->getViewFile($path);

        return $this->getContent($file, $data);
    }

    private function getViewFile($path)
    {
        $paths = config('view.path');
        if (empty($paths)) {
            $paths = [appPath('Views')];
        }

        $filename = trim($path, '/') . '.php';
        $file = null;
        foreach ($paths as $dir) {
            $file = nPath($dir, $filename);
            if (file_exists($file)) {
                break;
            }
        }
        return $file;
    }

    public function getErrorPage(ErrorBag $errorBag)
    {
        $code = $errorBag->getCode();
        //Fetch user error page for the error code
        $file = $this->getViewFile("errors/$code");

        //If page does not exist, fetch default error page
        if (!file_exists($file)) {
            $file = $this->getViewFile("errors/default");

            //We are left with no choose but to use our default error pages
            if (!file_exists($file)) {
                $file = basePath("core/html/errors/$code.php");
                if (!file_exists($file)) {
                    $file = basePath('core/html/errors/default.php');
                }
            }
        }

        return $this->getContent($file, ['bag' => $errorBag, 'errorBag' => $errorBag]);
    }

    private function getContent($file, $data = [])
    {
        $path = normalizePath($file);
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
}
