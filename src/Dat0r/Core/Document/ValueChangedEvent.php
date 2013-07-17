<?php

namespace Dat0r\Core\Document;

use Dat0r\Core\IEvent;
use Dat0r\Core\ValueHolder\IValueHolder;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Document\DocumentChangedEvent;

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
     * @var mixed $oldValue
     */
    private $oldValue;

    /**
     * Holds the new value of our field origin.
     *
     * @var mixed $newValue
     */
    private $newValue;

    /**
     * Holds the time at which the event was created.
     *
     * @var int $timestamp
     */
    private $timestamp;

    /**
     * Holds a possibly underlying aggrgate's value changed event.
     *
     * @var DocumentChangedEvent $aggregateEvent
     */
    private $aggregateEvent;

    /**
     * Creates a new ValueChangedEvent instance.
     *
     * @param IField $field
     * @param IValueHolder $old
     * @param IValueHolder $new
     * @param DocumentChangedEvent $aggregateEvent
     *
     * @return ValueChangedEvent
     */
    public static function create(
        IField $field, IValueHolder $old, IValueHolder $new, DocumentChangedEvent $aggregateEvent = NULL
    )
    {
        return new static($field, $old, $new, $aggregateEvent);
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
        return $this->oldValue;
    }

    /**
     * Returns the new value of the event's related field.
     *
     * @return IValueHolder
     */
    public function getNewValue()
    {
        return $this->newValue;
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
        return $this->aggregateEvent;
    }

    /**
     * Returns a string representation of the current event.
     *
     * @return string
     */
    public function __toString()
    {
        $stringRep = sprintf(
            "The `%s` field's value changed from '%s' to '%s'",
            $this->getField()->getName(),
            $this->getOldValue(),
            $this->getNewValue()
        );

        if (($aggregateEvent = $this->getAggregateEvent()))
        {
            $stringRep .= PHP_EOL . "The actual changed occured upon the field's aggregate though.";
            $stringRep .= PHP_EOL . $aggregateEvent;
        }

        return $stringRep;
    }

    /**
     * Constructs a new ValueChangedEvent instance.
     *
     * @param IField $field
     * @param IValueHolder $old
     * @param IValueHolder $new
     * @param DocumentChangedEvent $aggregateEvent
     */
    protected function __construct(
        IField $field, IValueHolder $old, IValueHolder $new, DocumentChangedEvent $aggregateEvent = NULL
    )
    {
        $this->field = $field;
        $this->timestamp = \time();
        $this->oldValue = $old;
        $this->newValue = $new;
        $this->aggregateEvent = $aggregateEvent;
    }
}
