<?php

namespace Dat0r\Runtime\Field;

class TextField extends Field
{
    public function getDefaultValue()
    {
        return $this->hasOption(self::OPT_VALUE_DEFAULT)
            ? (string)$this->getOption(self::OPT_VALUE_DEFAULT)
            : '';
    }
}
