<?php

namespace Dat0r\Runtime\Attribute\Value;

use Dat0r\Common\EventInterface;
use Dat0r\Common\Collection\CollectionInterface;
use Dat0r\Common\Collection\ListenerInterface;
use Dat0r\Common\Collection\CollectionChangedEvent;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\Entity\EntityChangedListenerInterface;
use Dat0r\Runtime\Entity\EntityChangedEvent;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Validator\Result\IncidentInterface;

/**
 * Basic ValueInterface implementation that all other Values should inherit from.
 */
abstract class Value implements ValueInterface, ListenerInterface, EntityChangedListenerInterface
{
    /**
     * Holds attribute which's data we are handling.
     *
     * @var AttributeInterface $attribute
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
     * @param AttributeInterface $attribute
     */
    public function __construct(AttributeInterface $attribute)
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
     * @return ResultInterface
     */
    public function set($value)
    {
        $attribute_validator = $this->getAttribute()->getValidator();
        $validation_result = $attribute_validator->validate($value);

        if ($validation_result->getSeverity() <= IncidentInterface::NOTICE) {
            $previous_value = $this->value;
            $this->value = $validation_result->getSanitizedValue();

            if (!$this->isEqualTo($previous_value)) {
                $this->propagateValueChangedEvent(
                    $this->createValueChangedEvent($previous_value)
                );
            }

            if ($this->value instanceof CollectionInterface) {
                $this->value->addListener($this);
            }
            if ($this->value instanceof EntityList) {
                $this->value->addEntityChangedListener($this);
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
     * @param ValueChangedListenerInterface $listener
     */
    public function addValueChangedListener(ValueChangedListenerInterface $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->push($listener);
        }
    }

    /**
     * Removes a given listener as from our list of value-changed listeners.
     *
     * @param ValueChangedListenerInterface $listener
     */
    public function removedValueChangedListener(ValueChangedListenerInterface $listener)
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
     * Handles entity changed events that are sent by our aggregated entity.
     *
     * @param EntityChangedEvent $event
     */
    public function onEntityChanged(EntityChangedEvent $event)
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
     * @return AttributeInterface
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
     * @param EventInterface $event
     */
    protected function createValueChangedEvent($prev_value, EventInterface $event = null)
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
