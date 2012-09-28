<?php

namespace CMF\Core\Runtime\ValueHolder;

use CMF\Core\Runtime;
use CMF\Core\Runtime\Field;

/**
 * This is the default IValueHolder implementation used for text values.
 */
class TextValueHolder extends ValueHolder
{
    /** 
     * Tells whether a spefic IValueHolder instance's value is considered greater than 
     * the value of an other given IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan(IValueHolder $other)
    {
        $leftVal = $this->getValue();
        $rightVal = $other->getValue();
        return 0 < strcmp($leftVal, $rightVal);
    }

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered less than 
     * the value of an other given IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan(IValueHolder $other)
    {
        $this->checkComparisonCompatibility($other);
        $leftVal = $this->getValue();
        $rightVal = $other->getValue();
        return 0 > strcmp($leftVal, $rightVal);
    }

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo(IValueHolder $other)
    {
        return $this->getValue() === $other->getValue();
    }

    /**
     * Sets the value holder's (text) value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        parent::setValue((string)$value);
    }

    /**
     * Contructs a new ValueHolder instance from a given value.
     *
     * @param mixed $value 
     */
    protected function __construct(Field\IField $field, $value = NULL)
    {
        if (! ($field instanceof Field\TextField))
        {
            throw new Error\BadValueException(
                "Only instances of TextField my be associated with TextValueHolder."
            );
        }
        parent::__construct($field, $value);
    }
}
