<?php

namespace Dat0r\Runtime\Validation\Rule;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Validation\Report\Message;

class TextLengthRule extends Rule
{
    protected static $supported_features = array('min', 'max');

    protected function execute($value)
    {
        $success = true;

        if ($this->isFeatureEnabled('min')) {
            $min = $this->getOption('min');
            if (mb_strlen($value) < $min) {
                $success = false;
                $this->throwError('min', array('min' => $min));
            }
        }

        if ($this->isFeatureEnabled('max')) {
            $max = $this->getOption('max');
            if (mb_strlen($value) > $max) {
                $success = false;
                $this->throwError('max', array('max' => $max));
            }
        }

        if ($success) {
            $this->setSanitizedValue($value);
        }

        return $success;
    }

    protected function isFeatureEnabled($feature)
    {
        if (in_array($feature, self::$supported_features)) {
            if ($this->hasOption($feature)) {
                $value = $this->getOption($feature);
                return is_int($value) && $value > 0;
            }
        } else {
            throw new RuntimeException(
                sprintf(
                    "Invalid feature: '%s' given. Supported are: %s",
                    $feature,
                    implode(', ', self::$supported_features)
                )
            );
        }
    }
}
