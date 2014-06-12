<?php

namespace Dat0r\Common;

use ReflectionClass;

class Object implements IObject
{
    const OBJECT_TYPE = '@type';

    const ANNOTATION_HIDDEN_PROPERTY = 'hiddenProperty';

    /**
     * @hiddenProperty
     */
    protected $_hidden_properties;

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
        $data = array(self::OBJECT_TYPE => get_class($this));
        $hidden_properties = $this->getHiddenProperties();

        foreach (get_object_vars($this) as $prop => $value) {
            if (in_array($prop, $hidden_properties)) {
                continue;
            }

            if ($value instanceof IObject) {
                $data[$prop] = $value->toArray();
            } else {
                $data[$prop] = $value;
            }
        }

        return $data;
    }

    protected function getHiddenProperties()
    {
        if (!$this->_hidden_properties) {
            $this->_hidden_properties = array();
            $class = new ReflectionClass($this);

            foreach ($class->getProperties() as $property) {
                $annotations = $this->parseDocBlockAnnotations(
                    $property->getDocComment()
                );

                if (in_array(self::ANNOTATION_HIDDEN_PROPERTY, $annotations)) {
                    $this->_hidden_properties[] = $property->getName();
                }
            }
        }

        return $this->_hidden_properties;
    }

    protected function parseDocBlockAnnotations($doc_block)
    {
        $annotation_pattern = '~\*\s+@(\w+)~';
        $annotations = array();

        preg_match($annotation_pattern, $doc_block, $matches);

        if (count($matches) === 2) {
            $annotations[] = $matches[1];
        }

        return $annotations;
    }
}
