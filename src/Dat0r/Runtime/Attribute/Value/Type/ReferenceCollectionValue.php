<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\Type\ReferenceCollection;

/**
 * Default ValueInterface implementation used for reference value containment.
 */
class ReferenceCollectionValue extends Value
{
    /**
     * Tells whether a specific ValueInterface instance's value is considered equal to
     * the value of an other given ValueInterface.
     *
     * @param ValueInterface $other
     *
     * @return boolean
     */
    public function isEqualTo($other_value)
    {
        if ($other_value === null) {
            return false;
        }
        if ($this->get()->getSize() !== $other_value->getSize()) {
            return false;
        }

        foreach ($this->get() as $index => $document) {
            if (!$document->isEqualTo($other_value->getItem($index))) {
                return false;
            }
        }

        return true;
    }
}
