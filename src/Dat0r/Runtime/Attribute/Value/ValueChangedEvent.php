<?php

namespace Dat0r\Runtime\Attribute\Value;

use Dat0r\Common\EventInterface;
use Dat0r\Common\Object;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Entity\EntityChangedEvent;

/**
 * ValueChangedEvent(s) reflect state changes to a entity's underlying Value.
 * These events are fired everytime a entity attribute-value actually changes and can be used
 * to track state changes over time.
 */
class ValueChangedEvent extends Object implements EventInterface
{
    /**
     * Holds the event's attribute origin.
     * @var IAttribute
     */
    protected $attribute;

    /**
     * Holds the previous value of our attribute origin.
     *
     * @var mixed $prev_value
     */
    protected $prev_value;

    /**
     * Holds the new value of our attribute origin.
     *
     * @var mixed $value
     */
    protected $value;

    /**
     * Holds the time at which the event was created.
     *
     * @var int $timestamp
     */
    protected $timestamp;

    /**
     * Holds a possibly underlying aggrgate's value changed event.
     *
     * @var EntityChangedEvent $aggregate_event
     */
    protected $aggregate_event;

    /**
     * Constructs a new ValueChangedEvent instance.
     *
     * @param EntityChangedEvent $aggregate_event If the origin attribute is an aggregate, the bubbled event is passed
     */
    public function __construct(array $state = array())
    {
        parent::__construct($state);

        $this->timestamp = time();
    }

    /**
     * Returns the event's affected attribute.
     *
     * @return IAttribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Returns the previous value of the event's related attribute.
     *
     * @return mixed
     */
    public function getOldValue()
    {
        return $this->prev_value;
    }

    /**
     * Returns the new value of the event's related attribute.
     *
     * @return mixed
     */
    public function getNewValue()
    {
        return $this->value;
    }

    /**
     * Returns the event's creation time as a unix timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * If the originating attribute is an aggregate attribute,
     * this method returns an aggregated entity's underlying value changed event.
     *
     * @return ValueChangedEvent
     */
    public function getAggregateEvent()
    {
        return $this->aggregate_event;
    }

    /**
     * Returns a string representation of the current event.
     *
     * @return string
     */
    public function __toString()
    {
        $string_representation = sprintf(
            "The `%s` attribute's value changed from '%s' to '%s'",
            $this->getAttribute()->getName(),
            $this->getOldValue(),
            $this->getNewValue()
        );

        if (($aggregate_event = $this->getAggregateEvent())) {
            $string_representation .= PHP_EOL . "The actual changed occured upon the attribute's aggregate though.";
            $string_representation .= PHP_EOL . $aggregate_event;
        }

        return $string_representation;
    }
}
