<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\TextField;

/**
 * Default IValueHolder implementation used for text value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class TextValueHolder extends ValueHolder
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

        return 0 < strcmp($leftVal, $rightVal);
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

        return 0 > strcmp($leftVal, $rightVal);
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
        parent::setValue((string)$value);
    }

    /**
     * Contructs a new TextValueHolder instance from a given value.
     *
     * @param IField $field 
     * @param mixed $value 
     */
    protected function __construct(IField $field, $value = NULL)
    {
        if (! ($field instanceof TextField))
        {
            throw new Error\BadValueException(
                "Only instances of TextField my be associated with TextValueHolder."
            );
        }
        
        parent::__construct($field, $value);
    }
}
