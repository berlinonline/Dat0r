<?php

namespace Dat0r\Runtime\Attribute\EmbeddedEntityList;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Holds a list of entities as an EntityList.
 */
class EmbeddedEntityListValueHolder extends ValueHolder
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

        if (!$other_value instanceof \Dat0r\Runtime\Entity\EntityList) {
            var_dump(get_class($other_value));
        }

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
}
