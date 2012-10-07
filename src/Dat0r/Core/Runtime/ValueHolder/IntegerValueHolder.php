<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\Field;

/**
 * This is the default IValueHolder implementation used for integer values.
 */
class IntegerValueHolder extends ValueHolder
{
    /** 
     * Tells whether a spefic IValueHolder instance's value is considered greater than 
     * the value of an other given IValueHolder.
     *
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan(IValueHolder $other)
    {
        $leftVal = $this->getValue();
        $rightVal = $other->getValue();
        return $leftVal > $rightVal;
    }

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered less than 
     * the value of an other given IValueHolder.
     *
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan(IValueHolder $other)
    {
        $this->checkComparisonCompatibility($other);
        $leftVal = $this->getValue();
        $rightVal = $other->getValue();
        return $leftVal < $rightVal;
    }
    
    /** 
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo(IValueHolder $other)
    {
        return $this->getValue() === $other->getValue();
    }

    /**
     * Sets the value holder's (int) value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        parent::setValue((int)$value);
    }

    /**
     * Contructs a new ValueHolder instance from a given value.
     *
     * @param mixed $value 
     */
    protected function __construct(Field\IField $field, $value = NULL)
    {
        if (! ($field instanceof Field\IntegerField))
        {
            throw new Error\BadValueException(
                "Only instances of NumberField my be associated with NumberValueHolder."
            );
        }
        parent::__construct($field, $value);
    }
}
