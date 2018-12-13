<?php
namespace LiteFrame\Utility;

use Doctrine\Common\Inflector\Inflector as DoctrineInflector;

/**
 * {@inheritdoc}
 */
class Inflector extends DoctrineInflector
{
    public static function slugify($text)
    {
        return preg_replace('#[\\/_\s]#', '-', static::tableize($text));
    }

    public static function underscore($text)
    {
        return preg_replace('#[\-\s]#', '_', preg_replace('~(?<=\\w)([A-Z])~', '_$1', $text));
    }

    public static function redbeantable($name) {
        $table = strtolower(preg_replace('#[\\/_\-\s]#', '', $name));
        return static::pluralize($table);
    }
}
