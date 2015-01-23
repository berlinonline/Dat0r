<?php

namespace Dat0r\Runtime\Attribute\Type;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\TimestampRule;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

//preferred ISO8601 format (works well with js/momentjs): format('Y-m-d\TH:i:s.uP');
class Timestamp extends Attribute
{
    const DEFAULT_ACCEPT_STRINGS = true;
    const DEFAULT_FORCE_INTERNAL_TIMEZONE = true;
    const DEFAULT_INTERNAL_TIMEZONE_NAME = 'Etc/UTC';

    const OPTION_ACCEPT_STRINGS = 'accept_strings';
    const OPTION_FORCE_INTERNAL_TIMEZONE = 'force_internal_timezone';
    const OPTION_INTERNAL_TIMEZONE_NAME = 'internal_timezone_name';
    const OPTION_MAX = 'max';
    const OPTION_MIN = 'min';

    const FORMAT_ISO8601 = 'Y-m-d\TH:i:s.uP';

    /**
     * Constructs a new attribute instance with some default options.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = array())
    {
        if (!array_key_exists(self::OPTION_FORCE_INTERNAL_TIMEZONE, $options)) {
            $options[self::OPTION_FORCE_INTERNAL_TIMEZONE] = self::DEFAULT_FORCE_INTERNAL_TIMEZONE;
        }

        if (!array_key_exists(self::OPTION_INTERNAL_TIMEZONE_NAME, $options)) {
            $options[self::OPTION_INTERNAL_TIMEZONE_NAME] = self::DEFAULT_INTERNAL_TIMEZONE_NAME;
        }

        if (!array_key_exists(self::OPTION_ACCEPT_STRINGS, $options)) {
            $options[self::OPTION_ACCEPT_STRINGS] = self::DEFAULT_ACCEPT_STRINGS;
        }

        parent::__construct($name, $options);
    }

    public function getDefaultValue()
    {
        $dti = null;

        $default_value = $this->getOption(self::OPTION_DEFAULT_VALUE, '');
        if (empty($default_value) || $default_value === 'null') {
            return $this->getNullValue();
        } elseif ($default_value === 'now') {
            $dti = DateTimeImmutable::createFromFormat(
                'U.u',
                sprintf('%.6F', microtime(true))
            );
        } elseif ($default_value instanceof DateTime) {
            $dti = DateTimeImmutable::createFromMutable($default_value);
        } elseif ($default_value instanceof DateTimeImmutable) {
            $dti = clone $default_value;
        } else {
            $dti = new DateTimeImmutable($default_value);
        }

        // set default internal timezone for the default timestamp created if necessary
        if ($this->getOption(self::OPTION_FORCE_INTERNAL_TIMEZONE, self::DEFAULT_FORCE_INTERNAL_TIMEZONE)) {
            $dti = $dti->setTimezone(
                new DateTimeZone(
                    $this->getOption(self::OPTION_INTERNAL_TIMEZONE_NAME, self::DEFAULT_INTERNAL_TIMEZONE_NAME)
                )
            );
        }

        if (!empty($dti)) {
            return $dti;
        }

        return $this->getNullValue();
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = [];

        if ($this->hasOption(self::OPTION_MIN)) {
            $options[self::OPTION_MIN] = $this->getOption(self::OPTION_MIN);
        }

        if ($this->hasOption(self::OPTION_MAX)) {
            $options[self::OPTION_MAX] = $this->getOption(self::OPTION_MAX);
        }

        $options[self::OPTION_ACCEPT_STRINGS] = $this->getOption(self::OPTION_ACCEPT_STRINGS, self::DEFAULT_ACCEPT_STRINGS);
        $options[self::OPTION_FORCE_INTERNAL_TIMEZONE] = $this->getOption(self::OPTION_FORCE_INTERNAL_TIMEZONE, self::DEFAULT_FORCE_INTERNAL_TIMEZONE);
        $options[self::OPTION_INTERNAL_TIMEZONE_NAME] = $this->getOption(self::OPTION_INTERNAL_TIMEZONE_NAME, self::DEFAULT_INTERNAL_TIMEZONE_NAME);

        $valid_datetime_rule = new TimestampRule('valid-timestamp', $options);

        $rules->push($valid_datetime_rule);

        return $rules;
    }
}
