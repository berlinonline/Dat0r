<?php

namespace Dat0r;

class Set extends ArrayList implements ISet
{
    const ITEMS_KEY_FIELD = 'items_key_field';

    protected $items_key_field;

    public function offsetSet($offset, $value)
    {
        $getter_method = 'get' . preg_replace(
            '/(?:^|_)(.?)/e',"strtoupper('$1')",
            $this->items_key_field
        );

        if (is_callable(array($value, $getter_method)))
        {
            $offset = $value->$getter_method();
        }
        else
        {
            throw new Exception(
                sprintf(
                    "Invalid collection-key-field '%s' given.",
                    $this->items_key_field
                )
            );
        }

        parent::offsetSet($offset, $value);
    }

    protected function applyParameters(array $parameters = array())
    {
        parent::applyParameters($parameters);

        if (!isset($parameters[self::ITEMS_KEY_FIELD]))
        {
            throw new Exception(
                sprintf(
                    "Missing key '%s' for parameters that where passed to '%s'.",
                    self::ITEMS_KEY_FIELD,
                    __METHOD__
                )
            );
        }

        $this->items_key_field = $parameters[self::ITEMS_KEY_FIELD];
    }
}
