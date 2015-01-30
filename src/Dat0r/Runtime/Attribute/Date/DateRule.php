<?php

namespace Dat0r\Runtime\Attribute\Date;

use Dat0r\Runtime\Attribute\Timestamp\TimestampRule;

class DateRule extends TimestampRule
{
    protected function execute($value)
    {
        $success = parent::execute($value);

        if (!$success) {
            return false;
        }

        $date = $this->getSanitizedValue();

        // forget about microsecond precision
        $date = $date->createFromFormat(
            DateAttribute::FORMAT_ISO8601_SIMPLE,
            $date->format(DateAttribute::FORMAT_ISO8601_SIMPLE)
        );

        // set time to 00:00:00
        $date = $date->setTime(
            $this->getOption(DateAttribute::OPTION_DEFAULT_HOUR, 0),
            $this->getOption(DateAttribute::OPTION_DEFAULT_MINUTE, 0),
            $this->getOption(DateAttribute::OPTION_DEFAULT_SECOND, 0)
        );

        $this->setSanitizedValue($date);

        return true;
    }
}
