<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\KeyValuesCollectionField;

/**
 * Default IValueHolder implementation used for key-values collection value containment.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class KeyValuesCollectionValueHolder extends ValueHolder
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
        else
        {
            foreach ($leftVal as $key => $values)
            {
                if (! isset($rightVal[$key]))
                {
                    $areEqual = FALSE;
                    break;
                }

                foreach ($values as $idx => $curValue)
                {
                    if (isset($rightVal[$key][$idx]) !== $curValue)
                    {
                        $areEqual = FALSE;
                        break;
                    }
                }
            }
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
        $attributes = array();
        $value = empty($value) ? array() : $value;
        foreach ($value as $key => $values)
        {
            $key = trim($key);
            if (! empty($key))
            {
                $attributes[$key] = array();
                foreach ($values as $curValue)
                {
                    $curValue = trim($curValue);
                    if (! empty($curValue))
                    {
                        $attributes[$key][] = $this->castValue($curValue);
                    }
                }
            }
        }
        
        parent::setValue($attributes);
    }

    /**
     * Contructs a new TextValueHolder instance from a given value.
     *
     * @param IField $field 
     * @param mixed $value 
     */
    protected function __construct(IField $field, $value = NULL)
    {
        if (! ($field instanceof KeyValuesCollectionField))
        {
            throw new Error\BadValueException(
                "Only instances of KeyValuesCollectionField my be associated with KeyValuesCollectionValueHolder."
            );
        }
        
        parent::__construct($field, $value);
    }

    protected function castValue($value)
    {
        $valueType = $this->getField()->getValueTypeConstraint();
        $validValues = TRUE;

        switch ($valueType) 
        {
            case 'integer':
            {
                $value = (int)$value;
                break;
            }

            case 'string':
            {
                 $value = (string)$value;
                break;
            }

            case 'boolean':
            {
                 $value = (bool)$value;
                break;
            }
        }

        return $value;
    }
}
