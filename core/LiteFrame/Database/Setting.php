<?php

namespace LiteFrame\Database;

/**
 * Description of Model.
 *
 * @author Victor Anuebunwa
 */
class Setting extends Model
{
    protected $primary = 'name';

    /**
     * Get settings.
     *
     * @param type $name Name of setting
     * @param type $cast Type to cast to. number, boolean and array supported
     *
     * @return type Value
     */
    public static function get($name, $default = null, $cast = null)
    {
        $setting = self::where('name', $name)->first();
        if (is_object($setting)) {
            $type = $cast ?: $setting->type;
            switch ($type) {
                case 'number':
                    return is_numeric($setting->value) ? floatval($setting->value) : $setting->value;
                case 'boolean':
                    return boolval($setting->value);
                case 'array':
                    return json_decode($setting->value, true);
                default:
                    return $setting->value;
            }
        } else {
            return $default;
        }
    }

    public static function set($name, $value, $type = null, $description = null, $system = 1)
    {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'array';
        }
        if (!is_object($setting = self::find($name))) {
            $setting = new self();
            $setting->name = $name;
            $setting->description = $description ?: str_replace('_', ' ', $name);
//            $setting->system = $system;
        }
        $setting->value = $value;
        if (isset($type)) {
            $setting->type = $type;
        }
        $setting->save();
    }
}
