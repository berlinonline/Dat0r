<?php

namespace Dat0r\Runtime\Validator\Rule\Bundle;

use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Validator\Result\IIncident;

/**
 * Supported options: min, max, ensure_utf8, trim
 */
class TextRule extends Rule
{
    protected function execute($value)
    {
        $success = true;

        if (!is_scalar($value)) {
            $this->throwError('non_scalar', array(), IIncident::CRITICAL);
            return false;
        }

        $ensure_utf8 = $this->getOption('ensure_utf8', false);
        if ($this->getOption('trim', false)) {
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

        if ($min = $this->getOption('min', false)) {
            if (mb_strlen($value) < $min) {
                $success = false;
                $this->throwError('min', array('min' => $min));
            }
        }

        if ($max = $this->getOption('max', false)) {
            if (mb_strlen($value) > $max) {
                $success = false;
                $this->throwError('max', array('max' => $max));
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
