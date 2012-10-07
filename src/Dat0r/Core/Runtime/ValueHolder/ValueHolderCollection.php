<?php

namespace Dat0r\Core\Runtime\ValueHolder;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\Document;
use Dat0r\Core\Runtime\Module;
use Dat0r\Core\Runtime\Field;

/**
 * @todo explain what valuesholders are and why they exist.
 */
class ValueHolderCollection implements Document\IAggregateChangedListener
{
    private $module;

    /**
     * @var array $values Holds the ValuesHolder's values.
     */
    private $values = array();

    private $valueChangedListeners = array();

    /**
     * Creates a new ValuesHolder instance.
     *
     * @param Dat0r\Core\Runtime\Document\IDocument $document
     *
     * @return Dat0r\Core\Runtime\Document\ValuesHolder
     */
    public static function create(Module\IModule $module)
    {
        return new self($module);
    }

    /**
     * Sets a value for a given field.
     *
     * @param Dat0r\Core\Runtime\Field\IField $field
     * @param Dat0r\Core\Runtime\ValueHolder\IValueHolder $value
     * @param boolean $override
     */
    public function set(Field\IField $field, $value, $override = TRUE)
    {
        if (! $this->getModule()->getFields()->has($field))
        {
            throw new Module\InvalidFieldException(
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
                Document\ValueChangedEvent::create($field, $prevValueObject, $newValueObject)
            );
        }
    }

    /** 
     * Returns the value for a specific field.
     *
     * @param Dat0r\Core\Runtime\Field\IField $field
     *
     * @return Dat0r\Core\Runtime\ValueHolder\IValueHolder
     */ 
    public function get(Field\IField $field)
    {
        if (! $this->has($field))
        {
            throw new Module\InvalidFieldException(
                sprintf("The given field %s is not set on the current ValuesHolder instance.", $field->getName())
            );
        }
        return $this->values[$field->getName()];
    }

    /**
     * Resets the value for a given field.
     *
     * @param Dat0r\Core\Runtime\Field\IField $field
     */
    public function reset(Field\IField $field)
    {
        if ($this->has($field))
        {
            unset($this->values[$field->getName()]);
        }
    }

    /**
     * Tells whether a value has been set for a given field.
     *
     * @param Dat0r\Core\Runtime\Field\IField $field
     *
     * @return boolean
     */
    public function has(Field\IField $field)
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

    public function notifyValueChanged(Document\ValueChangedEvent $event)
    {
        foreach ($this->valueChangedListeners as $listener)
        {
            $listener->onValueChanged($event);
        }
    }

    public function addValueChangedListener(Document\IValueChangedListener $valueChangedListener)
    {
        if (! in_array($valueChangedListener, $this->valueChangedListeners))
        {
            $this->valueChangedListeners[] = $valueChangedListener;
        }
    }

    public function onAggregateChanged(Field\AggregateField $field, Document\DocumentChangedEvent $event)
    {
        $valueChangedEvent = $event->getValueChangedEvent();

        $this->notifyValueChanged(Document\ValueChangedEvent::create(
            $field, 
            $valueChangedEvent->getOldValue(),
            $valueChangedEvent->getNewValue(),
            $event
        ));
    }

    /**
     * Constructs a new ValuesHolder instance.
     */
    protected function __construct(Module\IModule $module) 
    {
        $this->module = $module;
    }

    /**
     * Returns the valueholder instance's associated module.
     *
     * @return Dat0r\Core\Runtime\Module\IModule
     */
    protected function getModule()
    {
        return $this->module;
    }

    /**
     * Creates a IValueHolder instance for a given combination of IField and raw value.
     *
     * @param Dat0r\Core\Runtime\Field\IField $field
     * @param mixed $value
     *
     * @return Dat0r\Core\Runtime\ValueHolder\IValueHolder
     */
    protected function createValueHolder(Field\IField $field, $rawValue)
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
