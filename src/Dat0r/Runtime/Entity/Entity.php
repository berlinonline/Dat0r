<?php

namespace Dat0r\Runtime\Entity;

use Closure;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Object;
use Dat0r\Runtime\EntityTypeInterface;
use Dat0r\Runtime\Validator\Result\IncidentInterface;
use Dat0r\Runtime\Validator\Result\ResultMap;
use Dat0r\Runtime\ValueHolder\ValueChangedEvent;
use Dat0r\Runtime\ValueHolder\ValueChangedEventList;
use Dat0r\Runtime\ValueHolder\ValueChangedListenerInterface;
use Dat0r\Runtime\ValueHolder\ValueHolderInterface;
use Dat0r\Runtime\ValueHolder\ValueHolderMap;
use JsonSerializable;

/**
 * Entity generically implements the EntityInterface interface
 * and serves as a parent/ancestor to all generated and domain specific entity base-classes.
 * It provides generic value access via it's getValue(s) and setValue(s) methods.
 */
abstract class Entity extends Object implements EntityInterface, ValueChangedListenerInterface, JsonSerializable
{
    /**
     * Holds the entity's type.
     *
     * @var EntityTypeInterface $type
     */
    protected $type;

    /**
     * Holds a reference to the parent entity, if there is one.
     *
     * @var EntityInterface $parent;
     */
    protected $parent;

    /**
     * There is a ValueHolderInterface instance for each AttributeInterface of our type.
     * The property maps attribute_names to their respective valueholder instances
     * and is used for lookups during setValue(s) invocations.
     *
     * @var ValueHolderMap $value_holder_map
     */
    protected $value_holder_map;

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
     * @param EntityTypeInterface $type
     * @param array $data
     */
    public function __construct(EntityTypeInterface $type, array $data = array())
    {
        $this->type = $type;
        $this->listeners = new EntityChangedListenerList();
        $this->changes = new ValueChangedEventList();
        $this->validation_results = new ResultMap();

        // Setup a map of ValueHolderInterface specific to our type's attributes.
        // they hold the actual entity data.
        $this->value_holder_map = new ValueHolderMap();

        foreach ($type->getAttributes() as $attribute_name => $attribute) {
            $this->value_holder_map->setItem($attribute_name, $attribute->createValueHolder());
        }

        // Hydrate initial data ...
        $this->setValues($data);

        // ... then start tracking value-changed events coming from our valueholders.
        foreach ($this->value_holder_map as $value_holder) {
            $value_holder->addValueChangedListener($this);
        }
    }

    /**
     * Returns the entity's parent, if it has one.
     *
     * @return EntityInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the entity's parent once, if it isn't yet assigned.
     *
     * @param EntityInterface $parent
     */
    public function setParent(EntityInterface $parent)
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
     * @param mixed $attribute_value
     */
    public function setValue($attribute_name, $attribute_value)
    {
        $value_holder = $this->getValueHolderFor($attribute_name);

        $this->validation_results->setItem(
            $attribute_name,
            $value_holder->setValue($attribute_value)
        );

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
        $value_holder = $this->getValueHolderFor($attribute_name);

        return $value_holder->getValue();
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
        $value_holder = $this->getValueHolderFor($attribute_name);

        return !$value_holder->isNull();
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
     * Returns a (de)serializable representation of the attribute values. The
     * returned values MUST be acceptable as values on the attributes (that is,
     * their respective valueholders) to reconstitute them.
     *
     * Instead of implementing an explicit fromNative method use setValues to
     * recreate an entity from the given native representation.
     *
     * @return array of attribute values that can be used for serializing/deserializing
     */
    public function toNative()
    {
        $native_values = [];

        foreach ($this->value_holder_map as $attribute_name => $value_holder) {
            $native_values[$attribute_name] = $value_holder->toNative();
        }

        return $native_values;
    }

    /**
     * Returns an array representation of a entity's current value state.
     *
     * @return array
     */
    public function toArray()
    {
        $attribute_values = [ self::OBJECT_TYPE => get_class($this) ];

        foreach ($this->value_holder_map as $attribute_name => $value_holder) {
            $attribute_value = $value_holder->getValue();

            if (is_object($attribute_value) && is_callable([ $attribute_value, 'toArray' ])) {
                $attribute_values[$attribute_name] = $attribute_value->toArray();
            } else {
                $attribute_values[$attribute_name] = $value_holder->toNative();
            }
        }

        return $attribute_values;
    }

    /**
     * Tells whether this entity is considered equal to another given entity.
     * Entities are equal when they have the same type and values.
     *
     * @param EntityInterface $entity
     *
     * @return boolean true on both entities have the same type and values; false otherwise.
     */
    public function isEqualTo(EntityInterface $entity)
    {
        if ($entity->getType() !== $this->getType()) {
            return false;
        }

        if ($this->getType()->getAttributes()->getSize() !== $this->value_holder_map->getSize()) {
            return false;
        }

        foreach ($this->getType()->getAttributes()->getKeys() as $attribute_name) {
            $value_holder = $this->value_holder_map->getItem($attribute_name);
            if (!$value_holder->sameValueAs($entity->getValue($attribute_name))) {
                return false;
            }
        }

        return true;
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
        return !$this->validation_results || $this->validation_results->worstSeverity() <= IncidentInterface::NOTICE;
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
        $this->changes->clear();
    }

    /**
     * Returns the entity's type.
     *
     * @return EntityTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Attaches the given entity-changed listener.
     *
     * @param EntityChangedListenerInterface $listener
     */
    public function addEntityChangedListener(EntityChangedListenerInterface $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->push($listener);
        }
    }

    /**
     * Removes the given entity-changed listener.
     *
     * @param EntityChangedListenerInterface $listener
     */
    public function removeEntityChangedListener(EntityChangedListenerInterface $listener)
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
        // @todo Possible optimization: only track events for EmbedRoot entities,
        // what will save some memory when dealing with deeply nested embed structures.
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

    protected function getValueHolderFor($attribute_name)
    {
        $value_holder = $this->value_holder_map->getItem($attribute_name);

        if (!$value_holder) {
            throw new RuntimeException(
                sprintf(
                    'Unable to find value-holder for attribute: "%s" on entity-type "%s".'.
                    ' Maybe an invalid attribute-name or typo?',
                    $attribute_name,
                    $this->getType()->getName()
                )
            );
        }

        return $value_holder;
    }

    /**
     * @return array that may be used to generate a JSON representation of the entity
     */
    public function jsonSerialize()
    {
        /**
         * TODO what about line separator and paragraph separator characters
         * which are valid JSON but invalid javascript when not expressed as
         * escape sequence (\u2028, \u2029) in strings?
         */
        return $this->toNative();
    }
}
