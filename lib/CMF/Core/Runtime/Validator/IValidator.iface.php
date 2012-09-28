<?php

namespace CMF\Core\Runtime\Validator;

use CMF\Core\Runtime\Field;

/**
 * @todo explain what validators do etc.
 */
interface IValidator
{
    /**
     * Creates a new validator instance for a given field.
     *
     * @param CMF\Core\Runtime\Field\IField $field
     *
     * @return CMF\Core\Runtime\Validator\IValidator
     */
    public static function create(Field\IField $field);

    /**
     * Validates a given value thereby considering the state of the field
     * that a specific validator instance is related to.
     *
     * @param mixed $value
     *
     * @return boolean
     *
     * @todo Instead of just returning a bool we might want to return a validation report.
     *       This would be implemented inside our base class alowing inheriting implementations to throw errors
     *       and then just return true or false... @see AgaviValidator for a nice example.
     */
    public function validate($value);
}
