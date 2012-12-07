<?php

namespace Dat0r\Core\Runtime\Validator;

/**
 * Default implementation for validators that validate key-values collections.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class KeyValuesCollectionValidator extends Validator
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
        if (is_array($value))
        {   
            foreach ($value as $key => $values)
            {
                // @todo validate $values
            }
        }
        else if (! empty($value))
        {
            return FALSE;
        }

        return TRUE;
    }
}
