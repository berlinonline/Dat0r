<?php

namespace Dat0r\Runtime\Validator\Rule;

use Dat0r\Common\Object;
use Dat0r\Runtime\Validator\Result\Incident;
use Dat0r\Runtime\Validator\Result\IncidentMap;

abstract class Rule extends Object implements RuleInterface
{
    private $name;

    private $options;

    private $incidents;

    private $sanitized_value;

    /**
     * Validates the given value and should set a sanitized value via
     * ```$this->setSanitizedValue($sanitized_value);``` as a side effect.
     *
     * The method should not mutate the given value!
     *
     * The sanitized value will be given to the next validation rules by
     * the validator and will end up being used as the new value (if valid).
     *
     * @param mixed $value the valueholder's value to validate
     *
     * @return boolean true if valid; false otherwise.
     */
    abstract protected function execute($value);

    public function __construct($name, array $options = array())
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function apply($value)
    {
        $this->incidents = new IncidentMap(); // TODO does this have to be a map? accumulated throwError() in foreach?
        $this->sanitized_value = null;

        if (true === ($success = $this->execute($value))) {
            $this->sanitized_value = ($this->sanitized_value === null) ? $value : $this->sanitized_value;
        }

        return $success;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getOption($name, $default = null)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        } else {
            return $default;
        }
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getIncidents()
    {
        return $this->incidents;
    }

    public function getSanitizedValue()
    {
        return $this->sanitized_value;
    }

    protected function setSanitizedValue($sanitized_value)
    {
        $this->sanitized_value = $sanitized_value;
    }

    protected function throwError($name, array $parameters = array(), $severity = Incident::ERROR)
    {
        $this->incidents->setItem($name, new Incident($name, $parameters, $severity));
    }

    protected function toBoolean($value)
    {
        if (!is_string($value)) {
            return false;
        }

        $value = trim($value);
        if ($value === '') {
            return true; //  TRUE as it is a string and by default PHP thinks of this as truthy
        }

        $value = strtolower($value);
        if ($value === 'on' || $value === 'yes' || $value === 'true') {
            return true;
        } elseif ($value === 'off' || $value === 'no' || $value === 'false') {
            return false;
        }

        return true; // all other strings are true (as PHP likes to think)
    }
}
