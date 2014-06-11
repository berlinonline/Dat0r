<?php

namespace Dat0r\Runtime\Attribute\Type;

use Dat0r\Common\Uuid as UuidGenerator;

class Uuid extends Text
{
    /**
     * Returns the default value of the attribute.
     *
     * @return IValueHolder
     */
    public function getDefaultValue()
    {
        return UuidGenerator::generateVersion4();
    }
}
