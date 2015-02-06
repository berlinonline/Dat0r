<?php

namespace Dat0r\Runtime\Attribute\Integer;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for integer value containment.
 */
class IntegerValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param int $other_value number value to compare
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        if (!is_int($other_value)) {
            return false;
        }

        return $this->getValue() === $other_value;
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
        return $this->getValue();
    }

    /**
     * Returns the type of the value that is returned for the toNative() call.
     * This is used for typehints in code generation and might be used in other
     * layers (e.g. web form submissions) to handle things differently.
     *
     * @return string return type of the toNative() method
     */
    public function getNativeType()
    {
        return 'integer';
    }

    /**
     * Returns the type of the internal value of the valueholder. This can
     * be anything from 'string', 'array' or 'int' to a fully qualified class
     * name of the value object or PHP object used for storage internally.
     *
     * The returned type is the one returned by getValue() method calls.
     *
     * @return string type or FQCN of the internal value
     */
    public function getValueType()
    {
        return 'integer';
    }
}
