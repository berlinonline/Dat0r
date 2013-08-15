<?php

namespace Dat0r\Core\Validator;

/**
 * Default implementation for validators that validate key-values collections.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class KeyValuesCollectionValidator extends KeyValueValidator
{
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
            foreach ($value as $key => $values) {
                if (! $this->hasValidValues($values)) {
                    return false;
                }
            }
        } elseif (!is_null($value)) {
            return false;
        }

        return true;
    }
}
