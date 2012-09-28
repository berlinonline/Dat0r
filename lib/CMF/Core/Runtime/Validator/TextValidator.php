<?php

namespace CMF\Core\Runtime\Validator;

/**
 * Default implementation for validators that validate text.
 */
class TextValidator extends Validator
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
        return is_string($value);
    }
}