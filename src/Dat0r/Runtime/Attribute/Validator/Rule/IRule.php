<?php

namespace Dat0r\Runtime\Attribute\Validator\Rule;

use Dat0r\Runtime\Attribute\Validator\Result\IncidentMap;

interface IRule
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public function apply($value);

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed The option's value if the option is set, $default otherwise.
     */
    public function getOption($name, $default = null);

    /**
     * @return boolean
     */
    public function hasOption($name);

    /**
     * @return IncidentMap
     */
    public function getIncidents();

    /**
     * @return mixed
     */
    public function getSanitizedValue();
}
