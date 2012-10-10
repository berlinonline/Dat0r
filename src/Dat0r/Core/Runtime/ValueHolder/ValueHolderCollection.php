<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime\Module\IModule;
use Dat0r\Core\Runtime\Module\InvalidFieldException;
use Dat0r\Core\Runtime\Field\IField;
use Dat0r\Core\Runtime\Field\AggregateField;
use Dat0r\Core\Runtime\Document\IAggregateChangedListener;
use Dat0r\Core\Runtime\Document\IValueChangedListener;
use Dat0r\Core\Runtime\Document\ValueChangedEvent;
use Dat0r\Core\Runtime\Document\DocumentChangedEvent;

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
     * @var array $valueChangedListeners
     */
    private $valueChangedListeners = array();

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
    public function set(IField $field, $value, $override = TRUE)
    {
        if (! $this->getModule()->getFields()->has($field))
        {
            throw new InvalidFieldException(
                "Trying to set value for a field which is not contained by this ValueHolder's module."
            );
        }

        $newValueObject = $this->createValueHolder($field, $value);
        $newValueObject->freeze();
        
        $prevValueObject = $this->has($field) ? $this->get($field) : NullValue::create($field);

        if (! $this->has($field) || (! $prevValueObject->isEqualTo($newValueObject) && TRUE === $override))
        {
            $this->values[$field->getName()] = $newValueObject;
            $this->notifyValueChanged(
                ValueChangedEvent::create($field, $prevValueObject, $newValueObject)
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
        if (! $this->has($field))
        {
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
        if ($this->has($field))
        {
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
        foreach ($this->valueChangedListeners as $listener)
        {
            $listener->onValueChanged($event);
        }
    }

    /**
     * Registers a given listener as a recipient of value changed events.
     *
     * @param IValueChangedListener $valueChangedListener
     */
    public function addValueChangedListener(IValueChangedListener $valueChangedListener)
    {
        if (! in_array($valueChangedListener, $this->valueChangedListeners))
        {
            $this->valueChangedListeners[] = $valueChangedListener;
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

        $this->notifyValueChanged(ValueChangedEvent::create(
            $field, 
            $valueChangedEvent->getOldValue(),
            $valueChangedEvent->getNewValue(),
            $event
        ));
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
     * @param mixed $rawValue
     *
     * @return IValueHolder
     */
    protected function createValueHolder(IField $field, $rawValue)
    {
        $valueHolder = $field->createValueHolder($rawValue);
        if ($valueHolder instanceof AggregateValueHolder)
        {
            // Listen to all changes made to AggregateValueHolder instances corresponding to our related fields 
            // and propagte those changes to our listeners such as the ValuesHolder parent document.
            $valueHolder->addAggregateChangedListener($this);
        }
        return $valueHolder;
    }
}
