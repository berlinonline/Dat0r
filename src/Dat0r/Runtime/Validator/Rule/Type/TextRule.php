<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Result\IncidentInterface;

/**
 * Supported options: min, max, ensure_utf8, trim
 */
class TextRule extends Rule
{
    const OPTION_MIN = 'min';
    const OPTION_MAX = 'max';
    const OPTION_TRIM = 'trim';
    const OPTION_ENSURE_UTF8 = 'ensure_utf8';

    protected function execute($value)
    {
        $success = true;

        if (!is_scalar($value)) {
            $this->throwError('non_scalar', array(), IncidentInterface::CRITICAL);
            return false;
        }

        $ensure_utf8 = $this->getOption(self::OPTION_ENSURE_UTF8, false);
        if ($this->getOption(self::OPTION_TRIM, false)) {
            if ($ensure_utf8) {
                $pattern = '/^[\pZ\pC]*+(?P<trimmed>.*?)[\pZ\pC]*+$/usDS';
            } else {
                $pattern = '/^\s*+(?P<trimmed>.*?)\s*+$/sDS';
            }
            if (preg_match($pattern, $value, $matches)) {
                $value = $matches['trimmed'];
            }
        }
        if ($ensure_utf8) {
            // $value = utf8_decode($value);
        }

        if ($min = $this->getOption(self::OPTION_MIN, false)) {
            if (mb_strlen($value) < $min) {
                $success = false;
                $this->throwError(self::OPTION_MIN, array(self::OPTION_MIN => $min));
            }
        }

        if ($max = $this->getOption(self::OPTION_MAX, false)) {
            if (mb_strlen($value) > $max) {
                $success = false;
                $this->throwError(self::OPTION_MAX, array(self::OPTION_MAX => $max));
            }
        }
        // handle non-sgml characters
        $value = preg_replace('/[\x1-\x8\xB-\xC\xE-\x1F]/', '', $value);

        if ($success) {
            $this->setSanitizedValue($value);
        }

        return $success;
    }
}
