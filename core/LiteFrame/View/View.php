<?php

namespace LiteFrame\View;

use Exception;
use LiteFrame\Exception\ErrorBag;

class View
{
    public function fetch($path, $data = [])
    {
        $file = base_path('app/Views/' . trim($path, '/') . '.php');

        return $this->getContent($file, $data);
    }

    public function getErrorPage(ErrorBag $errorBag)
    {
        $code = $errorBag->getCode();
        //Fetch user error page for this code
        $file = base_path("app/Views/errors/$code.php");

        //If page does not exist, fetch default error page
        if (!file_exists($file)) {
            $file = base_path('app/Views/errors/default.php');
        }

        //We are left with no choose but to use our default error page
        if (!file_exists($file)) {
            $file = base_path('core/html/errors/default.php');
        }

        return $this->getContent($file, ['bag' => $errorBag]);
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
