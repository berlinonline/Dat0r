<?php

namespace Dat0r;

class ObjectSet extends ObjectList implements ISet
{
    const ITEMS_KEY_FIELD = 'items_key_field';

    protected $items_key_field;

    public static function create(array $data = array())
    {
        $object_set = parent::create($data);

        if (!isset($data[self::ITEMS_KEY_FIELD]))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    "Missing key '%s' for data given to '%s'.",
                    self::ITEMS_KEY_FIELD,
                    __METHOD__
                )
            );
        }

        $object_set->items_key_field = $data[self::ITEMS_KEY_FIELD];

        return $object_set;
    }

    public function offsetSet($offset, $value)
    {
        $getter_method = sprintf('get%s', ucfirst($this->items_key_field));

        if (is_callable(array($value, $getter_method)))
        {
            $offset = $value->$getter_method();
        }
        else
        {
            throw new \Exception(
                sprintf(
                    "Invalid collection-key-field '%s' given.",
                    $this->items_key_field
                )
            );
        }

        parent::offsetSet($offset, $value);
    }
}
