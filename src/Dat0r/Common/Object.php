<?php

namespace Dat0r\Common;

class Object implements IObject
{
    public static function create(array $data = array())
    {
        $object = new static();

        foreach ($data as $key => $value) {
            $camelcased_key = preg_replace_callback(
                '/_(.)/',
                function ($matches) {
                    return strtoupper($matches[1]);
                },
                $key
            );

            $setter_method = 'set' . $camelcased_key;
            if (is_callable(array($object, $setter_method))) {
                $object->$setter_method($value);
            } elseif (property_exists($object, $key)) {
                $object->$key = $value;
            }
        }

        return $object;
    }

    public function toArray()
    {
        $data = array('@type' => get_class($this));

        foreach (get_object_vars($this) as $prop => $value) {
            if ($value instanceof IObject) {
                $data[$prop] = $value->toArray();
            } else {
                $data[$prop] = $value;
            }
        }

        return $data;
    }
}
