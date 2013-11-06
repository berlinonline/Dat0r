<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Runtime\Error;

/**
 * Represents excpetions that reflect occurences of invalid values
 * during method execution.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class InvalidValueException extends Error\BadValueException
{
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
