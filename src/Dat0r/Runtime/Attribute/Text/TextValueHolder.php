<?php

namespace Dat0r\Runtime\Attribute\Text;

use Dat0r\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for text value containment.
 */
class TextValueHolder extends ValueHolder
{
    /**
     * Tells whether a specific ValueHolderInterface instance's value is considered equal to
     * the value of an other given ValueHolderInterface.
     *
     * @param ValueHolderInterface $other
     *
     * @return boolean
     */
    public function isEqualTo($right_value)
    {
        return $this->get() === $right_value;
    }
}
