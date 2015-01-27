<?php

namespace Dat0r\Runtime\Attribute\Timestamp;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
use Dat0r\Runtime\Attribute\Type\Timestamp;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class TimestampRule extends Rule
{
    protected function execute($value)
    {
        $default_timezone = new DateTimeZone(
            $this->getOption(
                TimestampAttribute::OPTION_INTERNAL_TIMEZONE_NAME,
                TimestampAttribute::DEFAULT_INTERNAL_TIMEZONE_NAME
            )
        );

        if (is_string($value)) {
            $accept_strings = $this->getOption(
                TimestampAttribute::OPTION_ACCEPT_STRINGS,
                TimestampAttribute::DEFAULT_ACCEPT_STRINGS
            );
            if (!$accept_strings) {
                $this->throwError('no_strings_acceptable', array(), IncidentInterface::CRITICAL);
                return false;
            }

            $dt = new DateTimeImmutable($value);
            if ($dt === false) {
                $this->throwError('invalid_string', array(), IncidentInterface::CRITICAL);
                return false;
            }
        } elseif ($value instanceof DateTime) {
            $dt = DateTimeImmutable::createFromMutable($value);
        } elseif ($value instanceof DateTimeImmutable) {
            $dt = clone $value;
        } else {
            $this->throwError('invalid_type', array(), IncidentInterface::CRITICAL);
            return false;
        }

        $force_internal_timezone = $this->getOption(
            TimestampAttribute::OPTION_FORCE_INTERNAL_TIMEZONE,
            TimestampAttribute::DEFAULT_FORCE_INTERNAL_TIMEZONE
        );
        if ($force_internal_timezone) {
            $dt = $dt->setTimezone($default_timezone);
        }

        if ($this->hasOption(TimestampAttribute::OPTION_MIN)) {
            $min = new DateTimeImmutable($this->getOption(TimestampAttribute::OPTION_MIN));
            $force_internal_timezone = $this->getOption(
                TimestampAttribute::OPTION_FORCE_INTERNAL_TIMEZONE,
                TimestampAttribute::DEFAULT_FORCE_INTERNAL_TIMEZONE
            );
            if ($force_internal_timezone) {
                $min->setTimezone($default_timezone);
            }

            // compare via PHP internal and then compare microseconds as well m(
            if (!( ($dt >= $min) && ((int)$dt->format('u') >= (int)$min->format('u')) )) {
                $this->throwError('min', array(), IncidentInterface::ERROR);
                return false;
            }
        }

        if ($this->hasOption(TimestampAttribute::OPTION_MAX)) {
            $max = new DateTimeImmutable($this->getOption('max'));
            $force_internal_timezone = $this->getOption(
                TimestampAttribute::OPTION_FORCE_INTERNAL_TIMEZONE,
                TimestampAttribute::DEFAULT_FORCE_INTERNAL_TIMEZONE
            );
            if ($force_internal_timezone) {
                $min->setTimezone($default_timezone);
            }

            if (!( ($dt <= $max) && ((int)$dt->format('u') <= (int)$max->format('u')) )) {
                $this->throwError('max', array(), IncidentInterface::ERROR);
                return false;
            }
        }

        $this->setSanitizedValue($dt);

        return true;
    }
}
