<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime\Error;
use Dat0r\Core\Runtime\Field\IField;
use Dat0r\Core\Runtime\Field\ReferenceField;

/**
 * Default IValueHolder implementation used for reference (id) value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
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
    public function isGreaterThan(IValueHolder $other)
    {
        return (! empty($this->getValue()) && empty($other->getValue()));
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
        return (empty($this->getValue()) && ! empty($other->getValue()));
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
        
        parent::setValue($value);
    }

    /**
     * Contructs a new ReferenceValueHolder instance from a given value.
     *
     * @param IField $field 
     * @param mixed $value 
     */
    protected function __construct(IField $field, $value = NULL)
    {
        if (! ($field instanceof ReferenceField))
        {
            throw new Error\BadValueException(
                "Only instances of ReferenceField my be associated with ReferenceValueHolder."
            );
        }

        parent::__construct($field, $value);
    }
}
