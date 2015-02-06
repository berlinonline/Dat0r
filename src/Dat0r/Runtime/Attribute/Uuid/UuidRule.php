<?php

namespace Dat0r\Runtime\Attribute\Uuid;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;

/**
 * Sanitized the input value to be a UUID v4.
 */
class UuidRule extends Rule
{
    protected function execute($value)
    {
        if (!is_string($value)) {
            $this->throwError('invalid_type', [ 'value' => $value ]);
            return false;
        }

        $trim = $this->toBoolean($this->getOption(UuidAttribute::OPTION_TRIM, false));
        if ($trim) {
            $value = trim($value);
        }

        $match_count = preg_match(
            '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
            $value
        );

        if ($match_count !== 1) {
            $this->throwError('invalid_uuidv4', [ 'value' => $value ]);
            return false;
        }

        $this->setSanitizedValue($value);

        return true;
    }
}
