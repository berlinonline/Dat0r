<?php

namespace Dat0r\Runtime\Attribute\Value;

use Dat0r\Runtime\Attribute\IAttribute;

/**
 * @todo Explain what valid holders are and what they are supposed to do.
 */
interface IValue
{
    /**
     * Returns the value holder's aggregated value.
     *
     * @return mixed
     */
    public function get();

    /**
     * Sets the value holder's value.
     *
     * @param string $value
     *
     * @return IResult
     */
    public function set($value);

    /**
     * Tells if a value holder has no value.
     *
     * @return boolean
     */
    public function isNull();

    /**
     * Tells whether a given IValue is considered being equal to a given other IValue.
     *
     * @param mixed $other
     *
     * @return boolean
     */
    public function isEqualTo($other_value);

    /**
     * Registers a given listener as a recipient of value changed events.
     *
     * @param IValueChangedListener $listener
     */
    public function addValueChangedListener(IValueChangedListener $listener);

    /**
     * Removes a given listener as from our list of value-changed listeners.
     *
     * @param IValueChangedListener $listener
     */
    public function removedValueChangedListener(IValueChangedListener $listener);
}
