<?php

namespace Dat0r\Core\Error;

/**
 * Reflects exceptions that occur in the context of invalid/bad values trying to be assigned somewhere.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class BadValueException extends Exception
{
    protected $moduleName;

    protected $fieldName;

    protected $value;

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function setValue($value)
    {
        if (is_scalar($value))
        {
            $this->value = $value;
        }
        else
        {
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
