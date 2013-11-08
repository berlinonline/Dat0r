<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Error\BadValueException;

/**
 * Represents excpetions that reflect occurences of invalid values
 * during method execution.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class InvalidValueException extends BadValueException
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
            "Invalid value: '%s', given for module '%s' and field '%s'.",
            $this->getValue(),
            $this->getModuleName(),
            $this->getFieldName()
        );
    }
}
