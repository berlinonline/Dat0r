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
     * Tells whether a given ValueHolderInterface is considered being equal to a given other ValueHolderInterface.
     *
     * @param mixed $other
     *
     * @return boolean
     */
    public function isEqualTo($other_value);

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
