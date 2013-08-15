<?php

namespace Dat0r\Core\Validator;

/**
 * Default implementation for validators that validate aggregates.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class AggregateValidator extends Validator
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
        // aggregate data is validated during hydrate or when setValue is called on an aggregate document.
        // so a simple check for an array value is enough to start with. enhance when needed.
        return is_array($value) || is_null($value);
    }
}
