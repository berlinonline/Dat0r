<?php

namespace Dat0r\Runtime\Field\Type;

use Dat0r\Common\Uuid;

class UuidField extends TextField
{
    /**
     * Returns the default value of the field.
     *
     * @return IValueHolder
     */
    public function getDefaultValue()
    {
        return Uuid::generateVersion4();
    }
}
