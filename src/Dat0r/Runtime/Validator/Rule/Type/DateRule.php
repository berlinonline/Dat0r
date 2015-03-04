<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

class DateRule extends TimestampRule
{
    const OPTION_DEFAULT_HOUR = 'default_hour';
    const OPTION_DEFAULT_MINUTE = 'default_minute';
    const OPTION_DEFAULT_SECOND = 'default_second';

    const FORMAT_NATIVE = self::FORMAT_ISO8601_SIMPLE;

    protected function execute($value)
    {
        $success = parent::execute($value);

        if (!$success) {
            return false;
        }

        $date = $this->getSanitizedValue();

        // forget about microsecond precision
        $date = $date->createFromFormat(
            self::FORMAT_ISO8601_SIMPLE,
            $date->format(self::FORMAT_ISO8601_SIMPLE)
        );

        // set time to 00:00:00
        $date = $date->setTime(
            $this->getOption(self::OPTION_DEFAULT_HOUR, 0),
            $this->getOption(self::OPTION_DEFAULT_MINUTE, 0),
            $this->getOption(self::OPTION_DEFAULT_SECOND, 0)
        );

        $this->setSanitizedValue($date);

        return true;
    }
}
