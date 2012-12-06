<?php

namespace Dat0r\Core\Runtime\Document;

use Dat0r\Core\Runtime\Error;

/**
 * Represents excpetions that reflect occurences of invalid values
 * during method execution.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class InvalidValueException extends Error\BadValueException
{
    protected $fieldname;

    public function setFieldname($fieldname)
    {
        $this->fieldname = $fieldname;
    }

    public function getFieldname()
    {
        return $this->fieldname;
    }
}
