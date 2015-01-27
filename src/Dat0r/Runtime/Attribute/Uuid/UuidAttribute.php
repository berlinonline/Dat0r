<?php

namespace Dat0r\Runtime\Attribute\Uuid;

use Dat0r\Runtime\Attribute\Text\TextAttribute;
use Rhumsaa\Uuid\Uuid as UuidGenerator;

class UuidAttribute extends TextAttribute
{
    /**
     * Returns the default value of the attribute.
     *
     * @return ValueHolderInterface
     */
    public function getDefaultValue()
    {
        return self::generateVersion4();
    }

    public static function generateVersion4()
    {
        return UuidGenerator::uuid4()->toString();
    }
}
