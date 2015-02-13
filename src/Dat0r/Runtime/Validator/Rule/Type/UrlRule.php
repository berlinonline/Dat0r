<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

// @todo do some spoofchecking on the host part?
// @see http://stackoverflow.com/questions/17458876/php-spoofchecker-class
class UrlRule extends Rule
{
    protected function execute($value)
    {
        if (!is_string($value)) {
            $this->throwError('invalid_type', [], IncidentInterface::CRITICAL);
            return false;
        }

        $success = filter_var($value, FILTER_VALIDATE_URL);
        if (!$success) {
            $this->throwError('invalid_format');
            $success = false;
        } else {
            $this->setSanitizedValue($value);
        }

        return $success;
    }
}
