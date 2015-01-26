<?php

namespace Dat0r\Runtime\Entity;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Validator\Result\IIncident;
use Dat0r\Runtime\Validator\Result\ResultMap;
use Dat0r\Runtime\IEntityType;
use Dat0r\Runtime\Attribute\Type\ReferenceCollection;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Attribute\Value\IValue;
use Dat0r\Runtime\Attribute\Value\ValueMap;
use Dat0r\Runtime\Attribute\Value\IValueChangedListener;
use Dat0r\Runtime\Attribute\Value\ValueChangedEvent;
use Dat0r\Runtime\Attribute\Value\ValueChangedEventList;
use Dat0r\Common\Object;

/**
 * Entity generically implements the IEntity interface
 * and serves as a parent/ancestor to all generated and domain specific entity base-classes.
 * It provides generic value access via it's getValue(s) and setValue(s) methods.
 */
abstract class Entity extends Object implements IEntity, IValueChangedListener
{
    /**
     * Holds the entity's type.
     *
     * @var IEntityType $type
     */
    protected $type;

    /**
     * Holds a reference to the parent entity, if there is one.
     *
     * @var IEntity $parent;
     */
    protected $parent;

    /**
     * There is a IValue instance for each IAttribute of our type.
     * The '$values' property maps attribute_names to their dedicated valueholder instance
     * and is used for lookups during setValue(s) invocations.
     *
     * @var ValueMap $values
     */
    protected $values;

    /**
     * Holds a list of all events that were received since the entity was instanciated
     * or the 'markClean' method was called.
     *
     * @var ValueChangedEventList $changes
     */
    protected $changes;

    /**
     * Holds all listeners that are notified about entity changed.
     *
     * @var EntityChangedListenerList $listeners
     */
    protected $listeners;

    /**
     * Always holds the validation results for a prior setValue(s) invocation.
     * The results are held as a map where particular results can be accessed by attribute_name.
     * There will be a result for every attribute affected by a setValue(s) call.
     *
     * @var ResultMap $validation_results
     */
    protected $validation_results;

    /**
     * Create a entity specific to the given type and hydrate it with the passed data.
     *
     * @param IEntityType $type
     * @param array $data
     */
    public function __construct(IEntityType $type, array $data = array())
    {
        $this->type = $type;
        $this->listeners = new EntityChangedListenerList();
        $this->changes = new ValueChangedEventList();
        $this->validation_results = new ResultMap();

        // Setup a map of IValue specific to our type's attributes.
        // they hold the actual entity data.
        $this->values = new ValueMap();
        foreach ($type->getAttributes() as $attribute_name => $attribute) {
            $this->values->setItem($attribute_name, $attribute->createValue());
        }

        // Hydrate initial data ...
        $this->setValues($data);

        // ... then start tracking value-changed events coming from our valueholders.
        foreach ($this->values as $value) {
            $value->addValueChangedListener($this);
        }
    }

    /**
     * Returns the entity's parent, if it has one.
     *
     * @return IEntity
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the entity's parent once, if it isn't yet assigned.
     *
     * @param IEntity $parent
     */
    public function setParent(IEntity $parent)
    {
        if (!$this->parent) {
            $this->parent = $parent;
        }
        // @todo else throw an exception,
        // as a second call to setParent might imply a logic error?
    }

    /**
     * Sets a specific value by attribute_name.
     *
     * @param string $attribute_name
     * @param mixed $value
     */
    public function setValue($attribute_name, $attribute_value)
    {
        $value = $this->values->getItem($attribute_name);

        if (!$value) {
            throw new RuntimeException(
                "Unable to find IValue for attribute: '" . $attribute_name . "'. Invalid attribute_name?"
            );
        }

        $value_validation_result = $value->set($attribute_value);
        $this->validation_results->setItem($attribute_name, $value_validation_result);

        return $this->isValid();
    }

    /**
     * Batch set a given list of attribute values.
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($this->type->getAttributes()->getKeys() as $attribute_name) {
            if (array_key_exists($attribute_name, $values)) {
                $this->setValue($attribute_name, $values[$attribute_name]);
            }
        }

        return $this->isValid();
    }

    /**
     * Returns the value for a specific attribute.
     *
     * @param string $attribute_name
     *
     * @return mixed
     */
    public function getValue($attribute_name)
    {
        $value = $this->values->getItem($attribute_name);

        if (!$value) {
            throw new RuntimeException(
                "Unable to find IValue for attribute: '" . $attribute_name . "'. Invalid attribute_name?"
            );
        }

        return $value->get();
    }

    /**
     * Tells if the entity has a value set for a given attribute.
     *
     * @param string $attribute_name
     *
     * @return boolean
     */
    public function hasValue($attribute_name)
    {
        $value = $this->values->getItem($attribute_name);

        if (!$value) {
            throw new RuntimeException(
                "Unable to find IValue for attribute: '" . $attribute_name . "'. Invalid attribute_name?"
            );
        }

        return !$value->isNull();
    }

    /**
     * Returns the values of all our attributes or a just specific attribute subset,
     * that can be defined by the optional '$attribute_names' parameter.
     *
     * @param array $attribute_names
     *
     * @return array
     */
    public function getValues(array $attribute_names = array())
    {
        $values = array();
        if (!empty($attribute_names)) {
            foreach ($attribute_names as $attribute_name) {
                $values[$attribute_name] = $this->getValue($attribute_name);
            }
        } else {
            foreach ($this->getType()->getAttributes() as $attribute) {
                $values[$attribute->getName()] = $this->getValue($attribute->getName());
            }
        }

        return $values;
    }

    /**
     * Returns an array representation of a entity's current value state.
     *
     * @return array
     */
    public function toArray()
    {
        $values = array();
        foreach ($this->getType()->getAttributes() as $attribute) {
            $value = $this->getValue($attribute->getName());
            if ($value instanceof Object) {
                $values[$attribute->getName()] = $value->toArray();
            } else {
                $values[$attribute->getName()] = $value;
            }
        }
        $values[self::OBJECT_TYPE] = get_class($this);

        return $values;
    }

    /**
     * Tells whether a spefic IEntity instance is considered equal to an other given entity.
     * entities are equal when they have both the same type and values.
     *
     * @param IEntity $entity
     *
     * @return boolean
     */
    public function isEqualTo(IEntity $entity)
    {
        if ($entity->getType() !== $this->getType()) {
            return false;
        }

        $is_equal = true;
        foreach ($this->getType()->getAttributes()->getKeys() as $attribute_name) {
            $attribute_value = $this->values->getItem($attribute_name);
            if (!$attribute_value->isEqualTo($entity->getValue($attribute_name))) {
                $is_equal = false;
                break;
            }
        }

        return $is_equal;
    }

    /**
     * Returns the validation results of a prior call to setValue(s).
     * There will be a result for each affected attribute.
     *
     * @return ResultMap
     */
    public function getValidationResults()
    {
        return $this->validation_results;
    }

    /**
     * Tells if a entity is considered being in a valid/safe state.
     * A entity is considered valid if no errors have occured while consuming data.
     *
     * @return boolean
     */
    public function isValid()
    {
        return !$this->validation_results || $this->validation_results->worstSeverity() <= IIncident::NOTICE;
    }

    /**
     * Returns a list of all events that have occured since the entity was instanciated
     * or the 'markClean' method was called.
     *
     * @return ValueChangedEventList
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Tells if the current entity instance is clean,
     * hence if it has any unhandled changes.
     *
     * @return boolean
     */
    public function isClean()
    {
        return $this->changes->getSize() === 0;
    }

    /**
     * Marks the current entity instance as clean, hence resets the all tracked changed.
     */
    public function markClean()
    {
        $this->changes = new ValueChangedEventList();
    }

    /**
     * Returns the entity's type.
     *
     * @return IEntityType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Attaches the given entity-changed listener.
     *
     * @param IEntityChangedListener $listener
     */
    public function addEntityChangedListener(IEntityChangedListener $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->push($listener);
        }
    }

    /**
     * Removes the given entity-changed listener.
     *
     * @param IEntityChangedListener $listener
     */
    public function removeEntityChangedListener(IEntityChangedListener $listener)
    {
        if ($this->listeners->hasItem($listener)) {
            $this->listeners->removeItem($listener);
        }
    }

    /**
     * Handles value-changed events that are received from the entity's value holders.
     *
     * @param ValueChangedEvent $event
     */
    public function onValueChanged(ValueChangedEvent $event)
    {
        // @todo Possible optimization: only track events for AggregateRoot entities,
        // what will save some memory when dealing with deeply nested aggregate structures.
        $this->changes->push($event);
        $this->propagateEntityChangedEvent($event);
    }

    /**
     * Translates a given value-changed event into a corresponding entity-changed event
     * and propagates the latter to all attached entity-changed listeners.
     *
     * @param ValueChangedEvent $event
     */
    protected function propagateEntityChangedEvent(ValueChangedEvent $event)
    {
        $event = new EntityChangedEvent($this, $event);
        foreach ($this->listeners as $listener) {
            $listener->onEntityChanged($event);
        }
    }
}
