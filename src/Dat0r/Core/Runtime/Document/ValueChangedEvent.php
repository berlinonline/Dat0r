<?php

namespace Dat0r\Core\Runtime\Document;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\ValueHolder;
use Dat0r\Core\Runtime\Field;

/**
 * ValueChangedEvent(s) reflect state changes to an document's underlying ValuesHolder.
 * These events are fired everytime an document field-value actually changes and can be used
 * to track state changes over time.
 */
class ValueChangedEvent implements Runtime\IEvent
{
    /**
     * @var Dat0r\Core\Runtime\Field\IField
     */
    private $field;

    /**
     * @var mixed $oldValue
     */
    private $oldValue;

    /**
     * @var mixed $oldValue
     */
    private $newValue;

    /**
     * @var int $timestamp
     */
    private $timestamp;

    /**
     * @var Dat0r\Core\Runtime\Document\DocumentChangedEvent $aggregateEvent
     */
    private $aggregateEvent;

    /**
     * Creates a new ValueChangedEvent instance.
     * 
     * @param Dat0r\Core\Runtime\Field\IField $field
     * @param array $values
     *
     * @return Dat0r\Core\Runtime\Document\ValueChangedEvent
     */
    public static function create(
        Field\Field $field, ValueHolder\IValueHolder $old, ValueHolder\IValueHolder $new, $aggregateEvent = NULL
    )
    {
        return new static($field, $old, $new, $aggregateEvent);
    }

    /**
     * Returns the event's affected field.
     *
     * @return Dat0r\Core\Runtime\Field\IField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns the previous value of the event's related field.
     *
     * @return Dat0r\Core\Runtime\ValueHolder\IValueHolder
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Returns the new value of the event's related field.
     *
     * @return Dat0r\Core\Runtime\ValueHolder\IValueHolder
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

    public function getAggregateEvent()
    {
        return $this->aggregateEvent;
    }

    /**
     * Constructs a new ValueChangedEvent instance.
     *
     * @param Dat0r\Core\Runtime\Field\IField $field
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $oldValue
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $newValue
     */
    protected function __construct(
        Field\Field $field, ValueHolder\IValueHolder $old, ValueHolder\IValueHolder $new, $aggregateEvent = NULL
    )
    {
        $this->field = $field;
        $this->timestamp = \time();
        $this->oldValue = $old;
        $this->newValue = $new;
        $this->aggregateEvent = $aggregateEvent;
    }
}
