<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime\Error;
use Dat0r\Core\Runtime\Field\IField;
use Dat0r\Core\Runtime\Field\IntegerField;

/**
 * Default IValueHolder implementation used for integer value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class IntegerValueHolder extends ValueHolder
{
    /** 
     * Tells whether a spefic IValueHolder instance's value is considered greater than 
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
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
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan(IValueHolder $other)
    {
        $leftVal = $this->getValue();
        $rightVal = $other->getValue();

        return $leftVal < $rightVal;
    }
    
    /** 
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
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
     * Contructs a new IntegerValueHolder instance from a given value.
     *
     * @param IField $field 
     * @param mixed $value 
     */
    protected function __construct(IField $field, $value = NULL)
    {
        if (! ($field instanceof IntegerField))
        {
            throw new Error\BadValueException(
                "Only instances of NumberField my be associated with NumberValueHolder."
            );
        }
        
        parent::__construct($field, $value);
    }
}
