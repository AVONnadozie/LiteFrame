<?php

use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/*
  |--------------------------------------------------------------------------
  | Include Core Helper Files
  |--------------------------------------------------------------------------
  |
 */
$ch_dir = CORE_DIR . DS . 'helpers';
$ch_files = scandir($ch_dir);
foreach ($ch_files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $path = $ch_dir . DS . $file;
    require $path;
}


/*
  |--------------------------------------------------------------------------
  | Include User Helper Functions
  |--------------------------------------------------------------------------
  |
 */
$helpers_files = basePath('/components/helpers');
requireAll($helpers_files);

/*
  |--------------------------------------------------------------------------
  | Configure application autoloader
  |--------------------------------------------------------------------------
  |
 */
spl_autoload_register('appAutoloader');


if (appIsLocal()) {

    if (class_exists('Symfony\Component\VarDumper\VarDumper')) {
        $cutomStyles = array(
            'default' => 'background-color:#fff; '
            . 'border:lightgray solid thin; '
            . 'color:#757575; '
            . 'line-height:1.2em; font:12px Menlo, Monaco, Consolas, monospace; '
            . 'word-wrap: break-word; '
            . 'white-space: pre-wrap; '
            . 'position:relative; z-index:99999; '
            . 'word-break: break-all',
            'num' => 'font-weight:bold; color:#e67231',
            'const' => 'font-weight:bold',
            'str' => 'font-weight:bold; color:#e67231',
            'note' => 'color:#1299DA',
            'ref' => 'color:#A0A0A0',
            'public' => 'color:#795da3',
            'protected' => 'color:#6f6767',
            'private' => 'color:#7da0b1',
            'meta' => 'color:#B729D9',
            'key' => 'color:#1299DA',
            'index' => 'color:#1299DA',
            'ellipsis' => 'color:#FF8400',
        );

        VarDumper::setHandler(function ($var) use ($cutomStyles) {
            $cloner = new VarCloner;
            if ('cli' === PHP_SAPI) {
                $dumper = new CliDumper;
            } else {
                $dumper = new HtmlDumper;
                $dumper->setStyles($cutomStyles);
            }
            $dumper->dump($cloner->cloneVar($var));
        });
    }
}
