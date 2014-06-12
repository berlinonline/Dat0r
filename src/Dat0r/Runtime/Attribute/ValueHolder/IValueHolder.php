<?php

namespace Dat0r\Runtime\Attribute\ValueHolder;

use Dat0r\Runtime\Attribute\IAttribute;

/**
 * @todo Explain what valid holders are and what they are supposed to do.
 */
interface IValueHolder
{
    /**
     * Creates a new IValueHolder instance for the given value.
     *
     * @param IAttribute $attribute
     *
     * @return IValueHolder
     */
    public static function create(IAttribute $attribute);

    /**
     * Returns the value holder's aggregated value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Sets the value holder's value.
     *
     * @param string $value
     *
     * @return IResult
     */
    public function setValue($value);

    /**
     * Tells if a value holder has a value.
     *
     * @return boolean
     */
    public function hasValue();

    /**
     * Tells if a value holder has no value.
     *
     * @return boolean
     */
    public function isValueNull();

    /**
     * Tells whether a given IValueHolder is considered being less than a given other IValueHolder.
     *
     * @param mixed $other
     *
     * @return boolean
     */
    public function isValueGreaterThan($righthand_value);

    /**
     * Tells whether a given IValueHolder is considered being less than a given other IValueHolder.
     *
     * @param mixed $other
     *
     * @return boolean
     */
    public function isValueLessThan($righthand_value);

    /**
     * Tells whether a given IValueHolder is considered being equal to a given other IValueHolder.
     *
     * @param mixed $other
     *
     * @return boolean
     */
    public function isValueEqualTo($righthand_value);

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
