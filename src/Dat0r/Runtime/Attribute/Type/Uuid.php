<?php

namespace Dat0r\Runtime\Attribute\Type;

use Rhumsaa\Uuid\Uuid as UuidGenerator;

class Uuid extends Text
{
    /**
     * Returns the default value of the attribute.
     *
     * @return ValueInterface
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
