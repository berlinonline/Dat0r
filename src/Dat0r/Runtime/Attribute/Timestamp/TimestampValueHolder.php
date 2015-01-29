<?php

namespace Dat0r\Runtime\Attribute\Timestamp;

use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\ValueHolder\ValueHolder;
use DateTimeInterface;

/**
 * Default implementation used for datetime/timestamp value containment.
 */
class TimestampValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param mixed $other_value DateTimeInterface or acceptable datetime string
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        $value = $this->getValue();

        return (
            ($value instanceof DateTimeInterface && $other_value instanceof DateTimeInterface) &&
            ($value == $other_value) && // no strict comparison as PHP then compares dates instead of instances
            ((int)$value->format('u') === (int)$other_value->format('u')) // compare the microseconds as well m(
        );
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if ($this->valueEquals($this->getAttribute()->getNullValue())) {
            return null;
        }

        return $this->getValue()->format(TimestampAttribute::FORMAT_ISO8601);
    }

    public function acceptable($value)
    {
        $validation_result = $this->getAttribute()->getValidator()->validate($value);
        if ($validation_result->getSeverity() <= IncidentInterface::NOTICE) {
            return true;
        }

        return false;
    }
}
