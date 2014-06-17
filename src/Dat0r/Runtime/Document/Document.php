<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Validator\Result\IIncident;
use Dat0r\Runtime\Validator\Result\ResultMap;
use Dat0r\Runtime\IDocumentType;
use Dat0r\Runtime\Attribute\Bundle\ReferenceCollection;
use Dat0r\Runtime\Attribute\Bundle\AggregateCollection;
use Dat0r\Runtime\Attribute\ValueHolder\IValueHolder;
use Dat0r\Runtime\Attribute\ValueHolder\ValueHolderMap;
use Dat0r\Runtime\Attribute\ValueHolder\IValueChangedListener;
use Dat0r\Runtime\Attribute\ValueHolder\ValueChangedEvent;
use Dat0r\Runtime\Attribute\ValueHolder\ValueChangedEventList;
use Dat0r\Common\Object;

/**
 * Document generically implements the IDocument interface
 * and serves as a parent/ancestor to all generated and domain specific document base-classes.
 * It provides generic value access via it's getValue(s) and setValue(s) methods.
 */
abstract class Document extends Object implements IDocument, IValueChangedListener
{
    /**
     * Holds the document's type.
     *
     * @var IDocumentType $type
     */
    protected $type;

    /**
     * Holds a reference to the parent document, if there is one.
     *
     * @var IDocument $parent;
     */
    protected $parent;

    /**
     * There is a IValueHolder instance for each IAttribute of our type.
     * The '$value_holders' property maps attribute_names to their dedicated valueholder instance
     * and is used for lookups during setValue(s) invocations.
     *
     * @var ValueHolderMap $value_holders
     */
    protected $value_holders;

    /**
     * Holds a list of all events that were received since the document was instanciated
     * or the 'markClean' method was called.
     *
     * @var ValueChangedEventList $changes
     */
    protected $changes;

    /**
     * Holds all listeners that are notified about document changed.
     *
     * @var DocumentChangedListenerList $listeners
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
     * Create a document specific to the given type and hydrate it with the passed data.
     *
     * @param IDocumentType $type
     * @param array $data
     */
    public function __construct(IDocumentType $type, array $data = array())
    {
        $this->type = $type;
        $this->listeners = new DocumentChangedListenerList();
        $this->changes = new ValueChangedEventList();
        // Setup a map of IValueHolder specific to our type's attributes.
        // they hold the actual document data.
        $this->value_holders = new ValueHolderMap();
        foreach ($type->getAttributes() as $attribute_name => $attribute) {
            $this->value_holders->setItem($attribute_name, $attribute->createValueHolder());
        }
        // Hydrate initial data ...
        $this->hydrate($data);
        // ... then start tracking value-changed events coming from our valueholders.
        foreach ($this->value_holders as $value_holder) {
            $value_holder->addValueChangedListener($this);
        }
    }

    /**
     * Returns the document's parent, if it has one.
     *
     * @return IDocument
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the document's parent once, if it isn't yet assigned.
     *
     * @param IDocument $parent
     */
    public function setParent(IDocument $parent)
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
    public function setValue($attribute_name, $value)
    {
        $this->validation_results = new ResultMap();
        $value_holder = $this->value_holders->getItem($attribute_name);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for attribute: '" . $attribute_name . "'. Invalid attribute_name?"
            );
        }
        $this->validation_results->setItem($attribute_name, $value_holder->setValue($value));

        return $this->isValid();
    }

    /**
     * Batch set a given list of attribute values.
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        // '$validation_results' is used to collect all particular validation results,
        // that are created each time we invoke '$this->setValue' for a given attribute value.
        $attribute_validation_results = new ResultMap();
        foreach ($this->type->getAttributes()->getKeys() as $attribute_name) {
            if (array_key_exists($attribute_name, $values)) {
                $this->setValue($attribute_name, $values[$attribute_name]);
                // memoize the current attribute validation result, after the prior call to setValue.
                $attribute_validation_results->setItem(
                    $attribute_name,
                    $this->validation_results->getItem($attribute_name)
                );
            }
        }
        $this->validation_results = $attribute_validation_results;

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
        $value_holder = $this->value_holders->getItem($attribute_name);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for attribute: '" . $attribute_name . "'. Invalid attribute_name?"
            );
        }

        return $value_holder->getValue();
    }

    /**
     * Tells if the document has a value set for a given attribute.
     *
     * @param string $attribute_name
     *
     * @return boolean
     */
    public function hasValue($attribute_name)
    {
        $value_holder = $this->value_holders->getItem($attribute_name);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for attribute: '" . $attribute_name . "'. Invalid attribute_name?"
            );
        }

        return $value_holder->hasValue();
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
     * Returns an array representation of a document's current value state.
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
     * Tells whether a spefic IDocument instance is considered equal to an other given document.
     * Documents are equal when they have both the same type and values.
     *
     * @param IDocument $document
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $document)
    {
        if ($document->getType() !== $this->getType()) {
            return false;
        }

        $is_equal = true;
        foreach ($this->getType()->getAttributes()->getKeys() as $attribute_name) {
            $value_holder = $this->value_holders->getItem($attribute_name);
            if (!$value_holder->isValueEqualTo($document->getValue($attribute_name))) {
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
     * Tells if a document is considered being in a valid/safe state.
     * A document is considered valid if no errors have occured while consuming data.
     *
     * @return boolean
     */
    public function isValid()
    {
        return !$this->validation_results || $this->validation_results->worstSeverity() <= IIncident::NOTICE;
    }

    /**
     * Returns a list of all events that have occured since the document was instanciated
     * or the 'markClean' method was called.
     *
     * @return ValueChangedEventList
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Tells if the current document instance is clean,
     * hence if it has any unhandled changes.
     *
     * @return boolean
     */
    public function isClean()
    {
        return $this->changes->getSize() === 0;
    }

    /**
     * Marks the current document instance as clean, hence resets the all tracked changed.
     */
    public function markClean()
    {
        $this->changes = new ValueChangedEventList();
    }

    /**
     * Returns the document's type.
     *
     * @return IDocumentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Attaches the given document-changed listener.
     *
     * @param IDocumentChangedListener $listener
     */
    public function addDocumentChangedListener(IDocumentChangedListener $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->push($listener);
        }
    }

    /**
     * Removes the given document-changed listener.
     *
     * @param IDocumentChangedListener $listener
     */
    public function removeDocumentChangedListener(IDocumentChangedListener $listener)
    {
        if ($this->listeners->hasItem($listener)) {
            $this->listeners->removeItem($listener);
        }
    }

    /**
     * Handles value-changed events that are received from the document's value holders.
     *
     * @param ValueChangedEvent $event
     */
    public function onValueChanged(ValueChangedEvent $event)
    {
        // @todo Possible optimization: only track events for AggregateRoot documents,
        // what will save some memory when dealing with deeply nested aggregate structures.
        $this->changes->push($event);
        $this->propagateDocumentChangedEvent($event);
    }

    /**
     * Initially hydrate the document.
     *
     * @param array $values
     */
    protected function hydrate(array $values)
    {
        $this->setValues($values);
    }

    /**
     * Translates a given value-changed event into a corresponding document-changed event
     * and propagates the latter to all attached document-changed listeners.
     *
     * @param ValueChangedEvent $event
     */
    protected function propagateDocumentChangedEvent(ValueChangedEvent $event)
    {
        $event = new DocumentChangedEvent($this, $event);
        foreach ($this->listeners as $listener) {
            $listener->onDocumentChanged($event);
        }
    }
}
