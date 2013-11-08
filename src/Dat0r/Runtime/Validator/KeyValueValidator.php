<?php

namespace Dat0r\Runtime\Validator;

use Dat0r\Common\Error\InvalidTypeException;

/**
 * Default implementation for validators that validate key-value.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class KeyValueValidator extends Validator
{
    protected static $value_types = array('integer', 'string', 'boolean', 'date', 'dynamic');

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
        if (is_array($value)) {
            if (!$this->hasValidValues($value)) {
                return false;
            }
        } elseif (!is_null($value)) {
            return false;
        }

        return true;
    }

    protected function hasValidValues(array $arr)
    {
        $value_type = $this->getField()->getValueTypeConstraint();
        $valid_values = true;

        foreach ($arr as $key => $value) {
            switch ($value_type) {
                case 'integer':
                    if (!is_numeric($value)) {
                        $valid_values = false;
                    }
                    break;

                case 'string':
                    if (!is_string($value)) {
                        $valid_values = false;
                    }
                    break;

                case 'boolean':
                    if (!is_bool($value)) {
                        $valid_values = false;
                    }
                    break;

                case 'date':
                    if (! ($value instanceof DateTime)) {
                        $valid_values = false;
                    }
                    break;

                default:
                    if (!is_scalar($value) && !($value instanceof DateTime)) {
                        $valid_values = false;
                    }
            }
        }

        return $valid_values;
    }
}
