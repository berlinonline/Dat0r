<?php

namespace Dat0r\Runtime\Attribute\Date;

use Dat0r\Runtime\Attribute\Timestamp\TimestampValueHolder;
use DateTimeInterface;

/**
 * Default implementation used for date value containment.
 */
class DateValueHolder extends TimestampValueHolder
{
    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if (!$this->getValue() instanceOf DateTimeInterface) {
            return '';
        }

        return $this->getValue()->format(
            $this->getAttribute()->getOption(
                DateAttribute::OPTION_FORMAT_NATIVE,
                DateAttribute::FORMAT_ISO8601_SIMPLE
            )
        );
    }
}
