<?php

namespace Dat0r\Runtime\Error;

/**
 * Reflects exceptions that occur in the context of invalid/bad values trying to be assigned somewhere.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class BadValueException extends Exception
{
    protected $module_name;

    protected $field_name;

    protected $value;

    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
    }

    public function getFieldName()
    {
        return $this->field_name;
    }

    public function setModuleName($module_name)
    {
        $this->module_name = $module_name;
    }

    public function getModuleName()
    {
        return $this->module_name;
    }

    public function setValue($value)
    {
        if (is_scalar($value)) {
            $this->value = $value;
        } else {
            $this->value = get_class($value);
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return sprintf(
            "An unexpected and probally corrupt value is given for module '%s' and field '%s'.",
            $this->getModuleName(),
            $this->getFieldName()
        );
    }
}
