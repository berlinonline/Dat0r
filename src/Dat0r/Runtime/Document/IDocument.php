<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Runtime\Module\IModule;
use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\Validator\Result\ResultMap;
use Dat0r\Runtime\ValueHolder\ValueChangedEvent;
use Dat0r\Runtime\ValueHolder\ValueChangedEventList;

/**
 * An IDocument is a generic container for structured data.
 * It provides access to values on a per field base.
 */
interface IDocument
{
    /**
     * Returns the document's parent, if it has one.
     *
     * @return IDocument
     */
    public function getParent();

    /**
     * Sets the document's parent once, if it isn't yet assigned.
     *
     * @param IDocument $parent
     */
    public function setParent(IDocument $parent);

    /**
     * Sets a specific value by fieldname.
     *
     * @param string $fieldname
     * @param mixed $value
     */
    public function setValue($fieldname, $value);

    /**
     * Batch set a given list of field values.
     *
     * @param array $values
     */
    public function setValues(array $values);

    /**
     * Returns the value for a specific field.
     *
     * @param string $fieldname
     *
     * @return mixed
     */
    public function getValue($fieldname);

    /**
     * Tells if the document has a value set for a given field.
     *
     * @param string $fieldname
     *
     * @return boolean
     */
    public function hasValue($fieldname);

    /**
     * Returns the values of all our fields or a just specific field subset,
     * that can be defined by the optional '$fieldnames' parameter.
     *
     * @param array $fieldnames
     *
     * @return array
     */
    public function getValues(array $fieldnames = array());

    /**
     * Returns an array representation of a document's current value state.
     *
     * @return array
     */
    public function toArray();

    /**
     * Tells whether a spefic IDocument instance is considered equal to an other given document.
     * Documents are equal when they have both the same module and values.
     *
     * @param IDocument $document
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $document);

    /**
     * Returns the validation results of a prior call to setValue(s).
     * There will be a result for each affected field.
     *
     * @return ResultMap
     */
    public function getValidationResults();

    /**
     * Tells if a document is considered being in a valid/safe state.
     * A document is considered valid if no errors have occured while consuming data.
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Returns a list of all events that have occured since the document was instanciated
     * or the 'markClean' method was called.
     *
     * @return ValueChangedEventList
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
     * Marks the current document instance as clean, hence resets the all tracked changed.
     */
    public function markClean();

    /**
     * Returns the document's module.
     *
     * @return IModule
     */
    public function getModule();

    /**
     * Attaches the given document-changed listener.
     *
     * @param IDocumentChangedListener $listener
     */
    public function addDocumentChangedListener(IDocumentChangedListener $listener);

    /**
     * Removes the given document-changed listener.
     *
     * @param IDocumentChangedListener $listener
     */
    public function removeDocumentChangedListener(IDocumentChangedListener $listener);
}
