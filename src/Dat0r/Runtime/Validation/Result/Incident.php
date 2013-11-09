<?php

namespace Dat0r\Runtime\Validation\Result;

use Dat0r\Common\Object;

class Incident extends Object implements IIncident
{
    protected $name;

    protected $severity;

    protected $parameters;

    public function __construct($name, array $parameters = array(), $severity = self::ERROR)
    {
        $this->name = $name;
        $this->severity = $severity;
        $this->parameters = $parameters;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSeverity()
    {
        return $this->severity;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameter($name, $default = null)
    {
        if ($this->hasParameter($name)) {
            return $this->parameters[$name];
        }

        return $default;
    }

    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }
}
