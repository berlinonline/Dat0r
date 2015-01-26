<?php

namespace Dat0r\Runtime\Validator\Rule\Type;

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
            $this->getOption(Timestamp::OPTION_INTERNAL_TIMEZONE_NAME, Timestamp::DEFAULT_INTERNAL_TIMEZONE_NAME)
        );

        if (is_string($value)) {
            if (!$this->getOption(Timestamp::OPTION_ACCEPT_STRINGS, Timestamp::DEFAULT_ACCEPT_STRINGS)) {
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

        if ($this->getOption(Timestamp::OPTION_FORCE_INTERNAL_TIMEZONE, Timestamp::DEFAULT_FORCE_INTERNAL_TIMEZONE)) {
            $dt = $dt->setTimezone($default_timezone);
        }

        if ($this->hasOption(Timestamp::OPTION_MIN)) {
            $min = new DateTimeImmutable($this->getOption(Timestamp::OPTION_MIN));
            $force_internal_timezone = $this->getOption(
                Timestamp::OPTION_FORCE_INTERNAL_TIMEZONE,
                Timestamp::DEFAULT_FORCE_INTERNAL_TIMEZONE
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

        if ($this->hasOption(Timestamp::OPTION_MAX)) {
            $max = new DateTimeImmutable($this->getOption('max'));
            $force_internal_timezone = $this->getOption(
                Timestamp::OPTION_FORCE_INTERNAL_TIMEZONE,
                Timestamp::DEFAULT_FORCE_INTERNAL_TIMEZONE
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
