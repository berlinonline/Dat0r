<?php

namespace Dat0r\Runtime\Attribute\Value\Type;

use Dat0r\Runtime\Attribute\Value\Value;
use DateTimeInterface;

/**
 * Default ValueInterface implementation used for datetime/timestamp value containment.
 */
class TimestampValue extends Value
{
    /**
     * Tells whether two datetime values are the same moment in time.
     *
     * @param DateTimeInterface $other_value datetime value to compare
     *
     * @return boolean true if moment in time is equal, false otherwise.
     */
    public function isEqualTo($other_value)
    {
        $value = $this->get();

        return (
            ($value instanceof DateTimeInterface && $other_value instanceof DateTimeInterface) &&
            ($value == $other_value) && // no strict comparison as PHP then compares the dates instead of the instances
            ((int)$value->format('u') === (int)$other_value->format('u')) // compare the microseconds as well m(
        );
    }
}
