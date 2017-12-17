<?php

namespace LiteFrame\View;

use Exception;

/**
 * Description of View.
 *
 * @author Victor Anuebunwa
 */
class View
{
    public function fetch($path, $data = [])
    {
        $file = WD.'/app/Views/'.trim($path, '/').'.php';

        return $this->getContent($file, $data);
    }

    public function getErrorPage($code = 404, $message = '')
    {
        //Fetch user error page for this code
        $file = WD."/app/Views/errors/$code.php";

        //If page does not exist, fetch default error page
        if (!file_exists($file)) {
            $file = WD.'/app/Views/errors/default.php';
        }

        //We are left with no choose but to use our default error page
        if (!file_exists($file)) {
            $file = WD.'/core/html/errors/default.php';
        }

        return $this->getContent($file, [
                    'code' => $code,
                    'message' => $message,
        ]);
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
