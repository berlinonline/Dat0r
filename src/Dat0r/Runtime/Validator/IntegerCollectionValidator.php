<?php

namespace Dat0r\Runtime\Validator;

/**
 * Default implementation for validators that validate integer collections.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class IntegerCollectionValidator extends IntegerValidator
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
            foreach ($value as $int) {
                if (! parent::validate($int)) {
                    return false;
                }
            }
        } elseif (!empty($value)) {
            return false;
        }

        return true;
    }
}
