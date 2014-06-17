<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Runtime\IDocumentType;
use Dat0r\Runtime\Attribute\ValueHolder\IValueHolder;
use Dat0r\Runtime\Validator\Result\ResultMap;
use Dat0r\Runtime\Attribute\ValueHolder\ValueChangedEvent;
use Dat0r\Runtime\Attribute\ValueHolder\ValueChangedEventList;

/**
 * An IDocument is a generic container for structured data.
 * It provides access to values on a per attribute base.
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
     * Tells if the document has a value set for a given attribute.
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
     * Returns an array representation of a document's current value state.
     *
     * @return array
     */
    public function toArray();

    /**
     * Tells whether a spefic IDocument instance is considered equal to an other given document.
     * Documents are equal when they have both the same type and values.
     *
     * @param IDocument $document
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $document);

    /**
     * Returns the validation results of a prior call to setValue(s).
     * There will be a result for each affected attribute.
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
     * Returns the document's type.
     *
     * @return IDocumentType
     */
    public function getType();

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
