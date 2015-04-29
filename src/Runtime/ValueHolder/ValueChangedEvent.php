<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\EventInterface;
use Dat0r\Common\Object;
use Dat0r\Runtime\Attribute\AttributeInterface;
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
     * @var string
     */
    protected $attribute_name;

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
     * @var EntityChangedEvent $embed_event
     */
    protected $embed_event;

    /**
     * Constructs a new ValueChangedEvent instance.
     *
     * @param EntityChangedEvent $embed_event If the origin attribute is an embed, the bubbled event is passed
     */
    public function __construct(array $state = [])
    {
        parent::__construct($state);

        $this->timestamp = time();
    }

    /**
     * Returns the event's affected attribute.
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attribute_name;
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
     * If the originating attribute is an embed attribute,
     * this method returns an embedd entity's underlying value changed event.
     *
     * @return ValueChangedEvent
     */
    public function getEmbeddedEvent()
    {
        return $this->embedded_event;
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
            $this->getAttributeName(),
            $this->getOldValue(),
            $this->getNewValue()
        );

        if (($embed_event = $this->getEmbeddedEvent())) {
            $string_representation .= PHP_EOL . "The actual changed occured upon the attribute's embed though.";
            $string_representation .= PHP_EOL . $embed_event;
        }

        return $string_representation;
    }
}
