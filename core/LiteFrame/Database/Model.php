<?php

namespace LiteFrame\Database;

use LiteFrame\Database\Traits\UsesRedBeanPHP;
use LiteFrame\Utility\Inflector;
use RedBeanPHP\OODBBean;

class Model extends OODBBean
{
    protected static $table;
    protected $updateTimestamps = true;
    
    private function __construct()
    {
    }
    
    public static function getTable()
    {
        if (empty(static::$table)) {
            $classname = get_called_class();
            $split = explode('\\', $classname);
            $name = array_pop($split);
            //Generate table name
            //static::$table = Inflector::tableize($name);
            static::$table = strtolower($name);
        }
        return static::$table;
    }
    
    /**
     * Dispenses this bean
     * @return $this
     */
    public static function instance($num = 1, $alwaysReturnArray = false)
    {
        $bean = DB::dispense(static::getTable(), $num, $alwaysReturnArray);
        $cast = new static;
        return cast($cast, $bean);
    }
    
    public function save()
    {
        if ($this->updateTimestamps) {
            if (!$this->id && !$this->created_at) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
        }
        return DB::store($this);
    }
}
