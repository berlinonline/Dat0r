<?php

namespace Dat0r\Runtime\Validator\Rule;

use Dat0r\Runtime\Validator\Result\Incident;
use Dat0r\Runtime\Validator\Result\IncidentMap;
use Dat0r\Common\Object;

abstract class Rule extends Object implements IRule
{
    private $name;

    private $options;

    private $incidents;

    private $sanitized_value;

    abstract protected function execute($value);

    public function __construct($name, array $options = array())
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function apply($value)
    {
        $this->incidents = new IncidentMap();
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
}
