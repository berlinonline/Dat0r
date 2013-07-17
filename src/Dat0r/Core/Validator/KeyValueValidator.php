<?php

namespace Dat0r\Core\Validator;

use Dat0r\Core\Error\InvalidTypeException;

/**
 * Default implementation for validators that validate key-value.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class KeyValueValidator extends Validator
{
    protected static $valueTypes = array('integer', 'string', 'boolean', 'date', 'dynamic');

    /**
     * Validates a given value thereby considering the state of the field
     * that a specific validator instance is related to.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function validate($value)
    {
        if (is_array($value))
        {   
            if (! $this->hasValidValues($value))
            {
                return FALSE;
            }
        }
        else if (! is_null($value))
        {
            return FALSE;
        }

        return TRUE;
    }

    protected function hasValidValues(array $arr)
    {
        $valueType = $this->getField()->getValueTypeConstraint();
        $validValues = TRUE;

        foreach ($arr as $key => $value)
        {
            switch ($valueType) 
            {
                case 'integer':
                {
                    if (! is_numeric($value))
                    {
                        $validValues = FALSE;
                    }
                    break;
                }

                case 'string':
                {
                    if (! is_string($value))
                    {
                        $validValues = FALSE;
                    }
                    break;
                }

                case 'boolean':
                {
                    if (! is_bool($value))
                    {
                        $validValues = FALSE;
                    }
                    break;
                }

                case 'date':
                {
                    if (! ($value instanceof DateTime))
                    {
                        $validValues = FALSE;
                    }
                    break;
                }

                default:
                {
                    if (! is_scalar($value) && ! ($value instanceof DateTime))
                    {
                        $validValues = FALSE;
                    }
                }
            }
        }

        return $validValues;
    }
}
