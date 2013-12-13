<?php

namespace Dat0r\Runtime\ValueHolder\Type;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\Type\ReferenceField;

/**
 * Default IValueHolder implementation used for reference value containment.
 */
class ReferenceValueHolder extends ValueHolder
{
    /**
     * Tells whether a spefic IValueHolder instance's value is considered greater than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueGreaterThan($righthand_value)
    {
        $lefthand_value = $this->getValue();

        return (!empty($lefthand_value) && empty($righthand_value));
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered less than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueLessThan($righthand_value)
    {
        $lefthand_value = $this->getValue();

        return (empty($lefthand_value) && !empty($righthand_value));
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isValueEqualTo($righthand_value)
    {
        if ($righthand_value === null) {
            return false;
        }
        if ($this->getValue()->getSize() !== $righthand_value->getSize()) {
            return false;
        }

        foreach ($this->getValue() as $index => $document) {
            if (!$document->isEqualTo($righthand_value->getItem($index))) {
                return false;
            }
        }

        return true;
    }
}
