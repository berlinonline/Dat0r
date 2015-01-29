<?php

namespace Dat0r\Runtime\Attribute\EntityReferenceList;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default ValueHolderInterface implementation for the EmbedCollection.
 * Holds an embed attribute's entities in form of a EntityList.
 */
class EntityReferenceListValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param EntityList $other_value list of entities
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        $entities = $this->getValue();

        if (count($entities) !== count($other_value)) {
            return false;
        }

        foreach ($entities as $index => $entity) {
            if (!$entity->isEqualTo($other_value->getItem($index))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if ($this->valueEquals($this->getAttribute()->getNullValue())) {
            return [];
        }

        $entities = [];

        foreach ($this->getValue() as $entity) {
            $entities[] = $entity->toNative();
        }

        return $entities;
    }
}
