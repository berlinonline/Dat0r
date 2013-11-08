<?php

namespace Dat0r\Runtime\Field;

class IntegerField extends Field
{
    public function getDefaultValue()
    {
        return $this->hasOption(self::OPT_VALUE_DEFAULT)
            ? (int)$this->getOption(self::OPT_VALUE_DEFAULT)
            : 0;
    }
}
