<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Entity\EntityList;

/**
 * Default ValueInterface implementation for the AggregateCollection.
 * Holds an aggregate attribute's entities in form of a EntityList.
 */
class AggregateCollectionValue extends Value
{
    /**
     * Tells if a given entity list contains the same entities.
     * The list is considered equal when entities with the same values occur in the same order
     * as in the valueholder's local value (EntityList).
     *
     * @param mixed $other_value
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        $lefthand_docs = $this->get();
        $is_equal = true;

        if (count($lefthand_docs) !== count($other_value)) {
            $is_equal = false;
        } else {
            foreach ($lefthand_docs as $index => $entity) {
                if (!$entity->isEqualTo($other_value->getItem($index))) {
                    $is_equal = false;
                }
            }
        }

        return $is_equal;
    }
}
