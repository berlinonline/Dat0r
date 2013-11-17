<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Collection\ICollection;
use Dat0r\Common\Collection\IListener;
use Dat0r\Common\Collection\CollectionChangedEvent;
use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Document\IDocumentChangedListener;
use Dat0r\Runtime\Document\DocumentChangedEvent;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Validator\Result\IIncident;

/**
 * Basic IValueHolder implementation that all other ValueHolders should inherit from.
 */
abstract class ValueHolder implements IValueHolder, IListener, IDocumentChangedListener
{
    /**
     * Holds field which's data we are handling.
     *
     * @var IField $field
     */
    private $field;

    /**
     * Holds the valueholder's current value.
     *
     * @var mixed $value
     */
    private $value;

    /**
     * Holds a list of listeners regisered to our value changed event.
     *
     * @var ValueChangedListenerList $listeners
     */
    private $listeners;

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
     * Contructs a new valueholder instance, that is dedicated to the given field.
     *
     * @param IField $field
     */
    public function __construct(IField $field)
    {
        $this->field = $field;
        $this->listeners = new ValueChangedListenerList();
    }

    /**
     * Returns the valueholder's value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the valueholder's value.
     *
     * @param mixed $value
     *
     * @return IResult
     */
    public function setValue($value)
    {
        $field_validator = $this->getField()->getValidator();
        $validation_result = $field_validator->validate($value);

        if ($validation_result->getSeverity() <= IIncident::NOTICE) {
            $previous_value = $this->value;
            $this->value = $validation_result->getSanitizedValue();

            if (!$this->isValueEqualTo($previous_value)) {
                $this->propagateValueChangedEvent(
                    $this->createValueChangedEvent($previous_value)
                );
            }

            if ($this->value instanceof ICollection) {
                $this->value->addListener($this);
            }
            if ($this->value instanceof DocumentList) {
                $this->value->addDocumentChangedListener($this);
            }
        }

        return $validation_result;
    }

    /**
     * Tells if the valueholder has a (non-null)value.
     *
     * @return boolean
     */
    public function hasValue()
    {
        return !$this->isNull();
    }

    /**
     * Tells if the valueholder's value is considered empty/null.
     *
     * @return boolean
     */
    public function isValueNull()
    {
        return $this->value === $this->field->getNullValue();
    }

    /**
     * Registers a given listener as a recipient of value changed events.
     *
     * @param IValueChangedListener $listener
     */
    public function addValueChangedListener(IValueChangedListener $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->push($listener);
        }
    }

    /**
     * Removes a given listener as from our list of value-changed listeners.
     *
     * @param IValueChangedListener $listener
     */
    public function removedValueChangedListener(IValueChangedListener $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->removeItem($listener);
        }
    }

    /**
     * Callback function that is invoked when an underlying collection value changes.
     *
     * @param CollectionChangedEvent $event
     */
    public function onCollectionChanged(CollectionChangedEvent $event)
    {
        // @todo need to find out what to use as the prev value here
        $this->propagateValueChangedEvent(
            $this->createValueChangedEvent($this->value)
        );
    }

    /**
     * Handles document changed events that are sent by our aggregated document.
     *
     * @param DocumentChangedEvent $event
     */
    public function onDocumentChanged(DocumentChangedEvent $event)
    {
        $value_changed_event = $event->getValueChangedEvent();

        $this->propagateValueChangedEvent(
            ValueChangedEvent::create(
                array(
                    'field' => $value_changed_event->getField(),
                    'prev_value' => $value_changed_event->getOldValue(),
                    'value' => $value_changed_event->getNewValue(),
                    'aggregate_event' => $event
                )
            )
        );
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

    /**
     * Propagates a given value changed event to all corresponding listeners.
     *
     * @param ValueChangedEvent $event
     */
    protected function createValueChangedEvent($prev_value, $event = null)
    {
        return ValueChangedEvent::create(
            array(
                'field' => $this->getField(),
                'prev_value' => $prev_value,
                'value' => $this->value,
                'aggregate_event' => $event
            )
        );
    }

    /**
     * Propagates a given value changed event to all corresponding listeners.
     *
     * @param ValueChangedEvent $event
     */
    protected function propagateValueChangedEvent(ValueChangedEvent $event)
    {
        foreach ($this->listeners as $listener) {
            $listener->onValueChanged($event);
        }
    }
}
