<?php

namespace Dat0r\Runtime\Validation\Rule;

use Dat0r\Runtime\Validation\Result\IIncident;

class EmailRule extends Rule
{
    protected function execute($value)
    {
        $success = true;

        if (!is_scalar($value)) {
            $this->throwError('invalid_type', array(), IIncident::CRITICAL);
            return false;
        }
        // From AgaviEmailValidator
        $pattern = sprintf(
            '/^[a-z0-9%1$s]+(\.[a-z0-9%1$s]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.[a-z]{2,6}$/iD',
            preg_quote('!#$%&\'*+-/=?^_`{|}~', '/')
        );

        if (!preg_match($pattern, $value)) {
            $this->throwError('invalid_format');
            $success = false;
        } else {
            $this->setSanitizedValue($value);
        }

        return $success;
    }
}
