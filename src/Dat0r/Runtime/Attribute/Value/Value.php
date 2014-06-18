<?php

namespace Dat0r\Runtime\Attribute\Value;

use Dat0r\Common\IEvent;
use Dat0r\Common\Collection\ICollection;
use Dat0r\Common\Collection\IListener;
use Dat0r\Common\Collection\CollectionChangedEvent;
use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Document\IDocumentChangedListener;
use Dat0r\Runtime\Document\DocumentChangedEvent;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Validator\Result\IIncident;

/**
 * Basic IValue implementation that all other Values should inherit from.
 */
abstract class Value implements IValue, IListener, IDocumentChangedListener
{
    /**
     * Holds attribute which's data we are handling.
     *
     * @var IAttribute $attribute
     */
    private $attribute;

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
     * Contructs a new valueholder instance, that is dedicated to the given attribute.
     *
     * @param IAttribute $attribute
     */
    public function __construct(IAttribute $attribute)
    {
        $this->attribute = $attribute;
        $this->listeners = new ValueChangedListenerList();
    }

    /**
     * Returns the valueholder's value.
     *
     * @return mixed
     */
    public function get()
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
    public function set($value)
    {
        $attribute_validator = $this->getAttribute()->getValidator();
        $validation_result = $attribute_validator->validate($value);

        if ($validation_result->getSeverity() <= IIncident::NOTICE) {
            $previous_value = $this->value;
            $this->value = $validation_result->getSanitizedValue();

            if (!$this->isEqualTo($previous_value)) {
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
     * Tells if the valueholder's value is considered empty/null.
     *
     * @return boolean
     */
    public function isNull()
    {
        return $this->value === $this->attribute->getNullValue();
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
        if ($this->listeners->hasItem($listener)) {
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
            new ValueChangedEvent(
                array(
                    'attribute' => $value_changed_event->getAttribute(),
                    'prev_value' => $value_changed_event->getOldValue(),
                    'value' => $value_changed_event->getNewValue(),
                    'aggregate_event' => $event
                )
            )
        );
    }

    /**
     * Returns the attribute that we are handling the data for.
     *
     * @return IAttribute
     */
    protected function getAttribute()
    {
        return $this->attribute;
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

    /**
     * Create a new value-changed event instance from the given info.
     *
     * @param mixed $prev_value
     * @param IEvent $event
     */
    protected function createValueChangedEvent($prev_value, IEvent $event = null)
    {
        return new ValueChangedEvent(
            array(
                'attribute' => $this->getAttribute(),
                'prev_value' => $prev_value,
                'value' => $this->value,
                'aggregate_event' => $event
            )
        );
    }
}
