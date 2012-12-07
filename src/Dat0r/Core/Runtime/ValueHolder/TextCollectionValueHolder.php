<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime\Error;
use Dat0r\Core\Runtime\Field\IField;
use Dat0r\Core\Runtime\Field\TextCollectionField;

/**
 * Default IValueHolder implementation used for text collection value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class TextCollectionValueHolder extends ValueHolder
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
        $leftCount = 0;
        $rightCount = 0;

        if (is_array($leftVal))
        {
            $leftCount = count($leftVal);
        }
        if (is_array($rightVal))
        {
            $rightCount = count($rightVal);
        }

        return $leftCount > $rightCount;
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
        $leftCount = 0;
        $rightCount = 0;

        if (is_array($leftVal))
        {
            $leftCount = count($leftVal);
        }
        if (is_array($rightVal))
        {
            $rightCount = count($rightVal);
        }

        return $leftCount > $rightCount;
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
        $leftVal = $this->getValue();
        $rightVal = $other->getValue();
        $leftCount = 0;
        $rightCount = 0;
        $areEqual = TRUE;

        if (is_array($leftVal))
        {
            $leftCount = count($leftVal);
        }
        if (is_array($rightVal))
        {
            $rightCount = count($rightVal);
        }

        if (0 < $leftCount && $leftCount === $rightCount)
        {
            foreach ($leftVal as $idx => $text)
            {
                if ($rightVal[$idx] !== $text)
                {
                    $areEqual = FALSE;
                }
            }
        }
        else if ($leftCount !== $rightCount)
        {
            $areEqual = FALSE;
        }

        return $areEqual;
    }

    /**
     * Sets the value holder's (int) value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $values = array();
        foreach ($value as $text)
        {
            $text = trim($text);
            if (! empty($text))
            {
                $values[] = $text;
            }
        }
        
        parent::setValue($values);
    }

    /**
     * Contructs a new TextValueHolder instance from a given value.
     *
     * @param IField $field 
     * @param mixed $value 
     */
    protected function __construct(IField $field, $value = NULL)
    {
        if (! ($field instanceof TextCollectionField))
        {
            throw new Error\BadValueException(
                "Only instances of TextCollectionField my be associated with TextCollectionValueHolder."
            );
        }
        parent::__construct($field, $value);
    }
}
