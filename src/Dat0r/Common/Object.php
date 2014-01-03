<?php

namespace Dat0r\Common;

class Object implements IObject
{
    /**
     * Returns a new Object instance hydrated with the given state.
     *
     * @param array $state An array with property names as keys and property values as array values.
     *
     * @return IObject
     */
    public static function create(array $state = array())
    {
        $object = new static();

        foreach ($state as $property_name => $property_value) {
            $camelcased_property = preg_replace_callback(
                '/_(.)/',
                function ($matches) {
                    return strtoupper($matches[1]);
                },
                $property_name
            );

            $setter_method = 'set' . $camelcased_property;
            if (is_callable(array($object, $setter_method))) {
                $object->$setter_method($property_value);
            } elseif (property_exists($object, $property_name)) {
                $object->$property_name = $property_value;
            }
        }

        return $object;
    }

    /**
     * Return an array representation of the current object.
     * The array will contain the object's property names as keys
     * and the property values as array values.
     * Nested 'IObject' and 'Options' instances will also be turned into arrays.
     *
     * @return array
     */
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
