<?php

namespace Dat0r\Core\Validator;

/**
 * Default implementation for validators that validate text.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
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
        if (! is_string($value) && !empty($value))
        {
            return false;
        }

        $success = is_string($value) && !empty($value);

        if ($success && $this->getField()->hasOption('pattern'))
        {
            $pattern = $this->getField()->getOption('pattern');
            $success = (bool)preg_match($pattern, $value);
        }

        return $success || empty($value);
    }
}
