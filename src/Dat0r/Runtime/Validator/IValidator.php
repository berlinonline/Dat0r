<?php

namespace Dat0r\Runtime\Validator;

use Dat0r\Runtime\Field\IField;

/**
 * IValidators are responseable for validating document data on a per field/property level,
 * before it is set.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
interface IValidator
{
    /**
     * Creates a new validator instance for a given field.
     *
     * @param IField $field
     *
     * @return IValidator
     */
    public static function create(IField $field);

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
