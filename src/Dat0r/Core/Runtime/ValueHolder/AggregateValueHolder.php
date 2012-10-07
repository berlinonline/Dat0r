<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime;
use Dat0r\Runtime\Error;
use Dat0r\Core\Runtime\Field;
use Dat0r\Core\Runtime\Document;

/**
 * This is the default IValueHolder implementation used for holding module aggregates.
 */
class AggregateValueHolder extends ValueHolder implements Document\IDocumentChangedListener
{
    private $aggregateChangedListeners = array();

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered greater than 
     * the value of an other given IValueHolder.
     *
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan(IValueHolder $other)
    {
        return FALSE;
    }

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered less than 
     * the value of an other given IValueHolder.
     *
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan(IValueHolder $other)
    {
        return FALSE;
    }

    /** 
     * Tells whether a spefic IValueHolder instance's value is considered equal to 
     * the value of an other given IValueHolder.
     *
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo(IValueHolder $other)
    {
        return $this->getValue()->isEqualTo($other->getValue());
    }

    /**
     * Sets the value holder's (array) value.
     *
     * @param array $value
     */
    public function setValue($value)
    {
        $module = $this->getField()->getAggregateModule();
        $aggregateDocument = $module->createDocument($value);
        $aggregateDocument->addDocumentChangedListener($this);

        parent::setValue($aggregateDocument);
    }

    public function notifyAggregateChanged(Document\DocumentChangedEvent $event)
    {
        foreach ($this->aggregateChangedListeners as $listener)
        {
            $listener->onAggregateChanged($this->getField(), $event);
        }
    }

    public function addAggregateChangedListener(Document\IAggregateChangedListener $aggregateChangedListener)
    {
        if (! in_array($aggregateChangedListener, $this->aggregateChangedListeners))
        {
            $this->aggregateChangedListeners[] = $aggregateChangedListener;
        }
    }

    public function onDocumentChanged(Document\DocumentChangedEvent $event)
    {
        $this->notifyAggregateChanged($event);
    }

    /**
     * Contructs a new ValueHolder instance from a given value.
     *
     * @param mixed $value 
     */
    protected function __construct(Field\IField $field, $value = NULL)
    {
        if (! ($field instanceof Field\AggregateField))
        {
            throw new Error\BadValueException(
                "Only instances of AggregateField my be associated with AggregateValueHolder."
            );
        }
        parent::__construct($field, $value);
    }
}
