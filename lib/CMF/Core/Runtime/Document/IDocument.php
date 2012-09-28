<?php

namespace CMF\Core\Runtime\Document;

use CMF\Core\Runtime;
use CMF\Core\Runtime\Module;

/**
 * @todo explain what entries are and what they do.
 */
interface IDocument
{
    /**
     * Creates a new Document.
     *
     * @param CMF\Core\Runtime\Module\IModule $module
     * @param array $data
     *
     * @return CMF\Core\Runtime\Document\IDocument 
     */
    public static function create(Module\IModule $module, array $data = array());

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
     * @param boolean $raw Whether to return the raw value or the corresponding IValueHolder instance.
     *
     * @return CMF\Core\Runtime\ValueHolder\IValueHolder
     */
    public function getValue($fieldname, $raw = TRUE);

    /**
     * Returns the values of either all fields or a specific field subset
     * defined by the optional fieldnames parameter.
     *
     * @param array $fieldnames
     * @param boolean $raw Whether to return the raw value or the corresponding IValueHolder instance.
     *
     * @return array A list of CMF\Core\Runtime\ValueHolder\IValueHolder.
     */
    public function getValues(array $fieldnames = array(), $raw = TRUE);

    /**
     * Returns a list of unhandled changes.
     * 
     * @return array An list of CMF\Core\Runtime\Document\ValueChangedEvent.
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
     * @return CMF\Core\Runtime\Module\IModule
     */
    public function getModule();

    /** 
     * Tells whether a spefic IDocument instance is considered equal to an other given IDocument.
     *
     * @param CMF\Core\Runtime\Document\IDocument $other
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $other);
}
