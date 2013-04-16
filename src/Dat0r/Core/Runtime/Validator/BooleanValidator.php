<?php

namespace Dat0r\Core\Runtime\Validator;

/**
 * Default implementation for validators that validate boolean values.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class BooleanValidator extends Validator
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
        $castValue = $value;
        
        if (is_bool($castValue)) 
        {
            // noop
        } 
        else if (1 === $castValue || '1' === $castValue) 
        {
            $castValue = TRUE;
        } 
        else if (0 === $castValue || '0' === $castValue) 
        {
            $castValue = FALSE;
        } 
        else if (is_string($castValue)) 
        {
            $castValue = AgaviToolkit::literalize($castValue);
        }

        return is_bool($castValue);
    }
}
