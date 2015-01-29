<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Runtime\Attribute\AttributeInterface;

/**
 * @todo Explain what valid holders are and what they are supposed to do.
 */
interface ValueHolderInterface
{
    /**
     * Returns the value holder's embedd value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Sets the value holder's value.
     *
     * @param string $value
     *
     * @return ResultInterface
     */
    public function setValue($value);

    /**
     * Tells if a value holder has no value.
     *
     * @return boolean
     */
    public function isNull();

    /**
     * Tells whether the valueholder's value is considered to be the same as
     * the default value defined on the attribute.
     *
     * @return boolean
     */
    public function isDefault();

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative();

    /**
     * Tells whether the given value is considered equal to the internal value.
     *
     * @param mixed $other_value
     *
     * @return boolean
     */
    public function sameValueAs($other_value);

    /**
     * Tells whether the given valueholder is considered being equal to the
     * current instance. That is, class name and value are considered the same
     * whileas the actual attribute and entity may be different.
     *
     * @param ValueHolderInterface $other_value_holder
     *
     * @return boolean
     */
    public function isEqualTo(ValueHolderInterface $other_value_holder);

    /**
     * Registers a given listener as a recipient of value changed events.
     *
     * @param ValueChangedListenerInterface $listener
     */
    public function addValueChangedListener(ValueChangedListenerInterface $listener);

    /**
     * Removes a given listener as from our list of value-changed listeners.
     *
     * @param ValueChangedListenerInterface $listener
     */
    public function removedValueChangedListener(ValueChangedListenerInterface $listener);
}
