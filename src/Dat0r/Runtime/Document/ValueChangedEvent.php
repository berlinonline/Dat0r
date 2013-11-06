<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Runtime\IEvent;
use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Document\DocumentChangedEvent;

/**
 * ValueChangedEvent(s) reflect state changes to an document's underlying ValuesHolder.
 * These events are fired everytime an document field-value actually changes and can be used
 * to track state changes over time.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class ValueChangedEvent implements IEvent
{
    /**
     * Holds the event's field origin.
     * @var IField
     */
    private $field;

    /**
     * Holds the previous value of our field origin.
     *
     * @var mixed $old_value
     */
    private $old_value;

    /**
     * Holds the new value of our field origin.
     *
     * @var mixed $new_value
     */
    private $new_value;

    /**
     * Holds the time at which the event was created.
     *
     * @var int $timestamp
     */
    private $timestamp;

    /**
     * Holds a possibly underlying aggrgate's value changed event.
     *
     * @var DocumentChangedEvent $aggregate_event
     */
    private $aggregate_event;

    /**
     * Creates a new ValueChangedEvent instance.
     *
     * @param IField $field
     * @param IValueHolder $old
     * @param IValueHolder $new
     * @param DocumentChangedEvent $aggregate_event
     *
     * @return ValueChangedEvent
     */
    public static function create(
        IField $field,
        IValueHolder $old,
        IValueHolder $new,
        DocumentChangedEvent $aggregate_event = null
    ) {
        return new static($field, $old, $new, $aggregate_event);
    }

    /**
     * Returns the event's affected field.
     *
     * @return IField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns the previous value of the event's related field.
     *
     * @return IValueHolder
     */
    public function getOldValue()
    {
        return $this->old_value;
    }

    /**
     * Returns the new value of the event's related field.
     *
     * @return IValueHolder
     */
    public function getNewValue()
    {
        return $this->new_value;
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
     * If the originating field is an aggregate field,
     * this method returns an aggregated document's underlying value changed event.
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
            "The `%s` field's value changed from '%s' to '%s'",
            $this->getField()->getName(),
            $this->getOldValue(),
            $this->getNewValue()
        );

        if (($aggregate_event = $this->getAggregateEvent())) {
            $string_representation .= PHP_EOL . "The actual changed occured upon the field's aggregate though.";
            $string_representation .= PHP_EOL . $aggregate_event;
        }

        return $string_representation;
    }

    /**
     * Constructs a new ValueChangedEvent instance.
     *
     * @param IField $field
     * @param IValueHolder $old
     * @param IValueHolder $new
     * @param DocumentChangedEvent $aggregate_event If the origin field is an aggregate, the bubbled event is passed
     */
    protected function __construct (
        IField $field,
        IValueHolder $old,
        IValueHolder $new,
        DocumentChangedEvent $aggregate_event = null
    ) {
        $this->field = $field;
        $this->timestamp = \time();
        $this->old_value = $old;
        $this->new_value = $new;
        $this->aggregate_event = $aggregate_event;
    }
}
