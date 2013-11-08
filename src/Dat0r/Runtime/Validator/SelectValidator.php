<?php

namespace Dat0r\Runtime\Validator;

/**
 * Default implementation for validators that validate select options.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class SelectValidator extends TextValidator
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
        $multi_select_enabled = $this->getField()->getOption('multiple', false);
        $allowed_options = $this->getField()->getOption('options', array());

        if ($multi_select_enabled) {
            if (!is_array($value) && !empty($value)) {
                return false;
            } elseif (is_array($value)) {
                foreach ($value as $option) {
                    if (!$this->validateOptionValue($option)) {
                        return false;
                    }
                }
            }
        } else {
            if (!is_scalar($value)) {
                return false;
            } else {
                if (!$this->validateOptionValue($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function validateOptionValue($value)
    {
        $allowed_options = $this->getField()->getOption('options', array());
        if (!is_scalar($value)) {
            return false;
        } else {
            return empty($value) || in_array($value, $allowed_options);
        }
    }
}
