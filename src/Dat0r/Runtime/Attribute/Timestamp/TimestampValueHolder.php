<?php

namespace Dat0r\Runtime\Attribute\Timestamp;

use Dat0r\Runtime\ValueHolder\ValueHolder;
use DateTimeInterface;

/**
 * Default implementation used for datetime/timestamp value containment.
 */
class TimestampValueHolder extends ValueHolder
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
        $value = $this->getValue();

        return (
            ($value instanceof DateTimeInterface && $other_value instanceof DateTimeInterface) &&
            ($value == $other_value) && // no strict comparison as PHP then compares the dates instead of the instances
            ((int)$value->format('u') === (int)$other_value->format('u')) // compare the microseconds as well m(
        );
    }
}
