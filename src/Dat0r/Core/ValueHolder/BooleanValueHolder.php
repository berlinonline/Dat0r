<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\BooleanField;

/**
 * Default IValueHolder implementation used for integer value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class BooleanValueHolder extends ValueHolder
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
        return TRUE === $this->getValue() && FALSE === $other->getValue();
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
        return FALSE === $this->getValue() && TRUE === $other->getValue();
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
        parent::setValue((bool)$value);
    }

    /**
     * Contructs a new BooleanValueHolder instance from a given value.
     *
     * @param IField $field 
     * @param mixed $value 
     */
    protected function __construct(IField $field, $value = NULL)
    {
        if (! ($field instanceof BooleanField))
        {
            throw new Error\BadValueException(
                "Only instances of BooleanField my be associated with BooleanValueHolder only."
            );
        }

        parent::__construct($field, $value);
    }
}
