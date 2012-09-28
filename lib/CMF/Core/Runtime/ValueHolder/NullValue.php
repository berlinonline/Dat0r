<?php

namespace CMF\Core\Runtime\ValueHolder;

use CMF\Core\Runtime;

/**
 * NullObject implementation of the IValueHolder interface.
 * Represents empty, not-set, nothing-for-you-here value holder instances.
 */
class NullValue extends ValueHolder implements Runtime\INullObject
{
    /** 
     * Tells whether a spefic IValueHolder instance's value is considered greater than 
     * the value of an other given IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan(IValueHolder $other)
    {
        return FALSE;
    }

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered less than 
     * the value of an other given IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan(IValueHolder $other)
    {
        return ! $this->isEqualTo($other);
    }

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param CMF\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo(IValueHolder $other)
    {
        return ($other instanceof NullValue);
    }
}
