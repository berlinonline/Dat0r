<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Runtime\Module\IModule;
use Dat0r\Runtime\Module\InvalidFieldException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\AggregateField;
use Dat0r\Runtime\Document\IAggregateChangedListener;
use Dat0r\Runtime\Document\IValueChangedListener;
use Dat0r\Runtime\Document\ValueChangedEvent;
use Dat0r\Runtime\Document\DocumentChangedEvent;

/**
 * Represents a list of IValueHolder.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class ValueHolderCollection implements IAggregateChangedListener
{
    /**
     * Holds our associated module.
     *
     * @var IModule $module
     */
    private $module;

    /**
     * Holds the ValuesHolder's values.
     *
     * @var array $values
     */
    private $values = array();

    /**
     * Holds a list of listeners regisered to our value changed event.
     *
     * @var array $value_changed_listeners
     */
    private $value_changed_listeners = array();

    /**
     * Creates a new ValuesHolder instance.
     *
     * @param IModule $module
     *
     * @return ValuesHolder
     */
    public static function create(IModule $module)
    {
        return new self($module);
    }

    /**
     * Sets a value for a given field.
     *
     * @param IField $field
     * @param IValueHolder $value
     * @param boolean $override
     */
    public function set(IField $field, $value, $override = true)
    {
        if (!$this->getModule()->getFields()->hasKey($field->getName())) {
            throw new InvalidFieldException(
                "Trying to set value for a field which is not contained by this ValueHolder's module."
            );
        }

        $new_value_object = $this->createValueHolder($field, $value);

        $prev_value_object = $this->has($field) ? $this->get($field) : NullValue::create($field);
        $override_existing = !$prev_value_object->isEqualTo($new_value_object) && true === $override;

        if (!$this->has($field) || $override_existing) {

            $this->values[$field->getName()] = $new_value_object;
            $this->notifyValueChanged(
                ValueChangedEvent::create($field, $prev_value_object, $new_value_object)
            );
        }
    }

    /**
     * Returns the value for a specific field.
     *
     * @param IField $field
     *
     * @return IValueHolder
     */
    public function get(IField $field)
    {
        if (!$this->has($field)) {
            throw new InvalidFieldException(
                sprintf("The given field %s is not set on the current ValuesHolder instance.", $field->getName())
            );
        }
        return $this->values[$field->getName()];
    }

    /**
     * Resets the value for a given field.
     *
     * @param IField $field
     */
    public function reset(IField $field)
    {
        if ($this->has($field)) {
            unset($this->values[$field->getName()]);
        }
    }

    /**
     * Tells whether a value has been set for a given field.
     *
     * @param IField $field
     *
     * @return boolean
     */
    public function has(IField $field)
    {
        return isset($this->values[$field->getName()]);
    }

    /**
     * Returns an array representation of the ValueHolder's present state.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * Propagates a given value changed event to all corresponding listeners.
     *
     * @param ValueChangedEvent $event
     */
    public function notifyValueChanged(ValueChangedEvent $event)
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
     * Handles document changed events that are sent by aggregated documents.
     *
     * @param AggregateField $field
     * @param DocumentChangedEvent $event
     */
    public function onAggregateChanged(AggregateField $field, DocumentChangedEvent $event)
    {
        $valueChangedEvent = $event->getValueChangedEvent();

        $this->notifyValueChanged(
            ValueChangedEvent::create(
                $field,
                $valueChangedEvent->getOldValue(),
                $valueChangedEvent->getNewValue(),
                $event
            )
        );
    }

    /**
     * Constructs a new ValuesHolder instance.
     *
     * @param IModule $module
     */
    protected function __construct(IModule $module)
    {
        $this->module = $module;
    }

    /**
     * Returns the valueholder instance's associated module.
     *
     * @return IModule
     */
    protected function getModule()
    {
        return $this->module;
    }

    /**
     * Creates a IValueHolder instance for a given combination of IField and raw value.
     *
     * @param IField $field
     * @param mixed $raw_value
     *
     * @return IValueHolder
     */
    protected function createValueHolder(IField $field, $raw_value)
    {
        $value_holder = $field->createValueHolder($raw_value);
        if ($value_holder instanceof AggregateValueHolder) {
            // Listen to all changes made to AggregateValueHolder instances corresponding to our related fields
            // and propagte those changes to our listeners such as the ValuesHolder parent document.
            $value_holder->addAggregateChangedListener($this);
        }
        return $value_holder;
    }
}
