<?php

namespace Dat0r\Runtime\Attribute\Text;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for text value containment.
 */
class TextValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param string $other_value string to compare to the internal one
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        return $this->getValue() === $other_value;
    }
}
