<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Error;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Validator\Result\IIncident;

/**
 * Basic IValueHolder implementation that all other ValueHolders should inherit from.
 */
abstract class ValueHolder implements IValueHolder
{
    /**
     * @var IField $field Holds field which's data we are handling.
     */
    private $field;

    /**
     * @var mixed $value Holds the ValueHolder's value.
     */
    private $value;

    /**
     * Holds a list of listeners regisered to our value changed event.
     *
     * @var array $value_changed_listeners
     */
    private $value_changed_listeners = array();

    /**
     * Creates a new IValueHolder instance from a given value.
     *
     * @param IField $field
     *
     * @return IValueHolder
     */
    public static function create(IField $field)
    {
        return new static($field);
    }

    /**
     * Contructs a new ValueHolder instance from a given value.
     *
     * @param IField $field
     */
    public function __construct(IField $field)
    {
        $this->field = $field;
    }

    /**
     * Returns the ValueHolder's value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the ValueHolder's value.
     *
     * @param mixed $value
     *
     * @return IResult
     */
    public function setValue($value)
    {
        $field_validator = $this->getField()->getValidator();
        $validation_result = $field_validator->validate($value);

        if ($validation_result->getSeverity() === IIncident::SUCCESS) {
            $previous_value = $this->value;
            $this->value = $validation_result->getSanitizedValue();

            if (!$this->isValueEqualTo($previous_value)) {
                $this->propagateValueChangedEvent(
                    ValueChangedEvent::create($this->getField(), $previous_value, $this->value)
                );
            }
        }

        return $validation_result;
    }

    public function hasValue()
    {
        return !$this->isNull();
    }

    public function isValueNull()
    {
        return $this->value === $this->field->getDefaultValue();
    }

    /**
     * Propagates a given value changed event to all corresponding listeners.
     *
     * @param ValueChangedEvent $event
     */
    public function propagateValueChangedEvent(ValueChangedEvent $event)
    {
        foreach ($this->value_changed_listeners as $listener) {
            $listener->onValueChanged($event);
        }
    }

    /**
     * Registers a given listener as a recipient of value changed events.
     *
     * @param IValueChangedListener $value_changed_listener
     */
    public function addValueChangedListener(IValueChangedListener $value_changed_listener)
    {
        if (!in_array($value_changed_listener, $this->value_changed_listeners)) {
            $this->value_changed_listeners[] = $value_changed_listener;
        }
    }

    /**
     * Returns the field that we are handling the data for.
     *
     * @return IField
     */
    protected function getField()
    {
        return $this->field;
    }
}
