<?php

namespace Dat0r\Core\Validator;

/**
 * Default implementation for validators that validate text collections.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class TextCollectionValidator extends TextValidator
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
            foreach ($value as $text)
            {
                if (! parent::validate($text))
                {
                    return FALSE;
                }
            }
        }
        else if (! is_null($value))
        {
            return FALSE;
        }

        return TRUE;
    }
}
