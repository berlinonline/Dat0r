<?php

namespace Dat0r\Runtime\Validator;

/**
 * Default implementation for validators that validate integers.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class IntegerValidator extends Validator
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
        // @todo implement more than this demo condition.
        return is_numeric($value) || empty($value);
    }
}
