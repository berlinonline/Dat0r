<?php

namespace Dat0r\Core\Field;

use Dat0r\Core\Freezable;
use Dat0r\Core\Error;

/**
 * Represents a collection of IField.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class FieldCollection extends Freezable implements IFieldCollection
{
    /**
     * Holds a list of IField.
     *
     * @var array $fields
     */
    private $fields = array();

    /**
     * Creates a new field collection passing in an initial set of fields.
     *
     * @param array $fields
     *
     * @return FieldCollection
     */
    public static function create(array $fields = array())
    {
        return new static($fields);
    }

    /**
     * Adds a field to the collection.
     *
     * @param IField $field
     */
    public function add(IField $field)
    {
        if ($this->isFrozen()) {
            throw new Error\ObjectImmutableException(
                "This FieldCollection instance is closed to modifications."
            );
        }

        if (!($field instanceof IField)) {
            throw new Error\InvalidImplementorException(
                "Fields passed to this method must be relate to the IField interface."
                . get_class($field) . " instance given instead."
            );
        }

        if ($this->has($field)) {
            throw new Error\BadValueException(
                "Field '". $field->getName() ."' already exists in this collection. "
                . "Fieldnames are required to be unique per collection."
            );
        }
        $this->fields[$field->getName()] = $field;
    }

    /**
     * Adds several fields to the collection at once.
     *
     * @param array $fields
     */
    public function addMore(array $fields)
    {
        foreach ($fields as $field) {
            $this->add($field);
        }
    }

    /**
     * Tells if a given field is allready inside the collection.
     *
     * @param IField $field
     *
     * @return boolean
     */
    public function has(IField $field)
    {
        // @todo Do a instance-equality check instead of just checking names?
        return isset($this->fields[$field->getName()]);
    }

    /**
     * Returns a field from the collection searching it by name.
     *
     * @param string $name
     *
     * @return IField
     */
    public function get($name)
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    /**
     * Returns the size of the collection.
     *
     * @return int
     */
    public function getSize()
    {
        return count(array_values($this->fields));
    }

    /**
     * Closes the collection to any further modifications.
     */
    public function freeze()
    {
        parent::freeze();

        foreach ($this->fields as $field) {
            $field->freeze();
        }
    }

    //
    // Iterator interface implementation
    //

    /**
     * Returns an array representation of the current collection instance.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->fields;
    }

    /**
     * Creates a new field collections.
     *
     * @param array $fields Initial list of fields to add.
     */
    protected function __construct(array $fields = array())
    {
        foreach ($fields as $field) {
            $this->add($field);
        }
    }

    public function count()
    {
        return count($this->fields);
    }

    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->fields[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        array_splice($this->fields, $offset, 1);
    }

    /**
     * Returns the field for the current collection cursor position.
     *
     * @return IField
     */
    public function current()
    {
        if ($this->valid()) {
            return current($this->fields);
        } else {
            return false;
        }
    }

    /**
     * Returns the fieldname for the current collection cursor position.
     *
     * @return string
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * Returns the next field in our collection,
     * thereby forwarding the collection cursor's position.
     *
     * @return IField
     */
    public function next()
    {
        return next($this->fields);
    }

    /**
     * Resets our collection cursor and return the first field.
     *
     * @return IField
     */
    public function rewind()
    {
        reset($this->fields);
    }

    /**
     * Tells whether the collection cursor's current position is valid.
     *
     * @return boolean
     */
    public function valid()
    {
        return key($this->fields) !== null;
    }
}
