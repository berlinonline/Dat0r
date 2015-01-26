<?php

namespace Dat0r\Runtime\Entity;

use Dat0r\Runtime\IEntityType;
use Dat0r\Runtime\Attribute\Value\IValue;
use Dat0r\Runtime\Validator\Result\ResultMap;
use Dat0r\Runtime\Attribute\Value\ValueChangedEvent;
use Dat0r\Runtime\Attribute\Value\ValueChangedEventList;

/**
 * An IEntity is a generic container for structured data.
 * It provides access to values on a per attribute base.
 */
interface IEntity
{
    /**
     * Returns the entity's parent, if it has one.
     *
     * @return IEntity
     */
    public function getParent();

    /**
     * Sets the entity's parent once, if it isn't yet assigned.
     *
     * @param IEntity $parent
     */
    public function setParent(IEntity $parent);

    /**
     * Sets a specific value by attribute_name.
     *
     * @param string $attribute_name
     * @param mixed $value
     */
    public function setValue($attribute_name, $value);

    /**
     * Batch set a given list of attribute values.
     *
     * @param array $values
     */
    public function setValues(array $values);

    /**
     * Returns the value for a specific attribute.
     *
     * @param string $attribute_name
     *
     * @return mixed
     */
    public function getValue($attribute_name);

    /**
     * Tells if the entity has a value set for a given attribute.
     *
     * @param string $attribute_name
     *
     * @return boolean
     */
    public function hasValue($attribute_name);

    /**
     * Returns the values of all our attributes or a just specific attribute subset,
     * that can be defined by the optional '$attribute_names' parameter.
     *
     * @param array $attribute_names
     *
     * @return array
     */
    public function getValues(array $attribute_names = array());

    /**
     * Returns an array representation of a entity's current value state.
     *
     * @return array
     */
    public function toArray();

    /**
     * Tells whether a spefic IEntity instance is considered equal to an other given entity.
     * entities are equal when they have both the same type and values.
     *
     * @param IEntity $entity
     *
     * @return boolean
     */
    public function isEqualTo(IEntity $entity);

    /**
     * Returns the validation results of a prior call to setValue(s).
     * There will be a result for each affected attribute.
     *
     * @return ResultMap
     */
    public function getValidationResults();

    /**
     * Tells if a entity is considered being in a valid/safe state.
     * A entity is considered valid if no errors have occured while consuming data.
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Returns a list of all events that have occured since the entity was instanciated
     * or the 'markClean' method was called.
     *
     * @return ValueChangedEventList
     */
    public function getChanges();

    /**
     * Tells if the current entity instance is clean,
     * hence if it has any unhandled changes.
     *
     * @return boolean
     */
    public function isClean();

    /**
     * Marks the current entity instance as clean, hence resets the all tracked changed.
     */
    public function markClean();

    /**
     * Returns the entity's type.
     *
     * @return IEntityType
     */
    public function getType();

    /**
     * Attaches the given entity-changed listener.
     *
     * @param IEntityChangedListener $listener
     */
    public function addEntityChangedListener(IEntityChangedListener $listener);

    /**
     * Removes the given entity-changed listener.
     *
     * @param IEntityChangedListener $listener
     */
    public function removeEntityChangedListener(IEntityChangedListener $listener);
}
