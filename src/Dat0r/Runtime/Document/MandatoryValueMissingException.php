<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Runtime\Error;

/**
 * Reflects exceptions that occur in the context of invalid/bad values trying to be assigned somewhere.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class MandatoryValueMissingException extends Error\BadValueException
{
    public function __toString()
    {
        return sprintf(
            "Required value missing for module '%s' and field '%s'.",
            $this->getModuleName(),
            $this->getFieldName()
        );
    }
}
