<?php

namespace Dat0r\CodeGen\Schema;

abstract class BaseDefinition
{
    public static function create(array $data = array())
    {
        $module_schema = new static();

        foreach ($data as $key => $value)
        {
            if (property_exists($module_schema, $key))
            {
                $module_schema->$key = $value;
            }
        }

        return $module_schema;
    }

    public function toArray()
    {
        $data = array();

        foreach (get_object_vars($this) as $prop => $value)
        {
            if (is_object($value))
            {
                $data[$prop] = $value->toArray();
            }
            else
            {
                $data[$prop] = $value;
            }
        }

        return $data;
    }

    protected function __construct() {}
}
