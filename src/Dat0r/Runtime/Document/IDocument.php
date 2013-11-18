<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Runtime\Module\IModule;
use Dat0r\Runtime\ValueHolder\IValueHolder;

/**
 * An IDocument is a generic container for structured data.
 * It provides access to values on a per field base.
 */
interface IDocument
{
    /**
     * Sets a given list of values.
     *
     * @param array $values
     */
    public function setValues(array $values);

    /**
     * Sets a specific value by fieldname.
     *
     * @param string $fieldname
     * @param mixed $value
     */
    public function setValue($fieldname, $value);

    /**
     * Returns the value for a specific field.
     *
     * @param string $fieldname
     *
     * @return IValueHolder
     */
    public function getValue($fieldname);

    /**
     * Returns the values of either all fields or a specific field subset
     * defined by the optional fieldnames parameter.
     *
     * @param array $fieldnames
     *
     * @return array A list of IValueHolder.
     */
    public function getValues(array $fieldnames = array());

    /**
     * Tells if the document has a value set for a given field.
     *
     * @param string $fieldname
     *
     * @return boolean
     */
    public function hasValue($fieldname);

    /**
     * Returns a list of unhandled changes.
     *
     * @return array An list of ValueChangedEvent.
     */
    public function getChanges();

    /**
     * Tells if the current document instance is clean,
     * hence if it has any unhandled changes.
     *
     * @return boolean
     */
    public function isClean();

    /**
     * Marks the current document instance as clean,
     * hence resets the all tracked changed.
     */
    public function markClean();

    /**
     * Returns an entries module.
     *
     * @return IModule
     */
    public function getModule();

    /**
     * Tells whether a spefic IDocument instance is considered equal to an other given IDocument.
     *
     * @param IDocument $other
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $other);
}
