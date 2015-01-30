<?php

namespace Dat0r\Runtime\Attribute\Timestamp;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Rule\RuleList;

// preferred exchange format is FORMAT_ISO8601 ('Y-m-d\TH:i:s.uP')
class TimestampAttribute extends Attribute
{
    const DEFAULT_FORCE_INTERNAL_TIMEZONE = true;
    const DEFAULT_INTERNAL_TIMEZONE_NAME = 'Etc/UTC';

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

        parent::__construct($name, $options);
    }

    public function getDefaultValue()
    {
        $default_value = $this->getOption(self::OPTION_DEFAULT_VALUE, '');
        if (empty($default_value) || $default_value === 'null') {
            return $this->getNullValue();
        }

        $validation_result = $this->getValidator()->validate($default_value);
        if ($validation_result->getSeverity() > IncidentInterface::NOTICE) {
            throw new InvalidConfigException(
                sprintf(
                    "Configured default_value for attribute '%s'on entity type '%s' is not valid.",
                    $this->getName(),
                    $this->getType()->getName()
                )
            );
        }

        return $validation_result->getSanitizedValue();
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

        $options[self::OPTION_FORCE_INTERNAL_TIMEZONE] = $this->getOption(
            self::OPTION_FORCE_INTERNAL_TIMEZONE,
            self::DEFAULT_FORCE_INTERNAL_TIMEZONE
        );
        $options[self::OPTION_INTERNAL_TIMEZONE_NAME] = $this->getOption(
            self::OPTION_INTERNAL_TIMEZONE_NAME,
            self::DEFAULT_INTERNAL_TIMEZONE_NAME
        );

        $valid_datetime_rule = new TimestampRule('valid-timestamp', $options);

        $rules->push($valid_datetime_rule);

        return $rules;
    }
}
