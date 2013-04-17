<?php

namespace Dat0r\Core\Runtime\Field;

/**
 * Concrete implementation of the Field base class.
 * Stuff in here is dedicated to handling integer values.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class IntegerField extends Field
{
    public function getDefaultValue()
    {
        return $this->hasOption(self::OPT_VALUE_DEFAULT) ? (int)$this->getOption(self::OPT_VALUE_DEFAULT) : 0;
    }
}
