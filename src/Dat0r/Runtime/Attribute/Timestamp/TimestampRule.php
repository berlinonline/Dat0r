<?php

namespace Dat0r\Runtime\Attribute\Timestamp;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\Rule;
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
            if ($value === 'now') {
                $dt = DateTimeImmutable::createFromFormat(
                    'U.u',
                    sprintf('%.6F', microtime(true))
                );
            } elseif ($value === '') {
                // this is the toNative return value for the nullValue
                $dt = false;
            } else {
                $dt = new DateTimeImmutable($value);
            }

            if ($dt === false) {
                $this->throwError('invalid_string', [], IncidentInterface::CRITICAL);
                return false;
            }
        } elseif ($value instanceof DateTime) {
            if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
                $dt = DateTimeImmutable::createFromMutable($value);
            } else {
                $dt = DateTimeImmutable::createFromFormat(
                    TimestampAttribute::FORMAT_ISO8601,
                    $value->format(TimestampAttribute::FORMAT_ISO8601)
                );
            }
        } elseif ($value instanceof DateTimeImmutable) {
            $dt = clone $value;
        } else {
            $this->throwError('invalid_type', [], IncidentInterface::CRITICAL);
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
            $max = new DateTimeImmutable($this->getOption(TimestampAttribute::OPTION_MAX));
            $force_internal_timezone = $this->getOption(
                TimestampAttribute::OPTION_FORCE_INTERNAL_TIMEZONE,
                TimestampAttribute::DEFAULT_FORCE_INTERNAL_TIMEZONE
            );
            if ($force_internal_timezone) {
                $max->setTimezone($default_timezone);
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
