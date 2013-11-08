<?php

namespace Dat0r\Runtime\Document;

class MandatoryValueMissingException extends InvalidValueException
{
    public function __toString()
    {
        return sprintf(
            "Required value for module '%s' and field '%s' is missing.",
            $this->getModuleName(),
            $this->getFieldName()
        );
    }
}
