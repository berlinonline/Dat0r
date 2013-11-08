<?php

namespace Dat0r\Runtime\Field;

class BooleanField extends Field
{
    public function getDefaultValue()
    {
        if ($this->hasOption(self::OPT_VALUE_DEFAULT)) {
            return (bool)$this->getOption(self::OPT_VALUE_DEFAULT);
        }

        return false;
    }
}
