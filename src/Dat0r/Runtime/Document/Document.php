<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Validator\Result\IIncident;
use Dat0r\Runtime\Validator\Result\ResultMap;
use Dat0r\Runtime\Validator\Service as ValidationService;
use Dat0r\Runtime\Module\IModule;
use Dat0r\Runtime\Field\Type\ReferenceField;
use Dat0r\Runtime\Field\Type\AggregateField;
use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\ValueHolder\ValueHolderMap;
use Dat0r\Runtime\ValueHolder\IValueChangedListener;
use Dat0r\Runtime\ValueHolder\ValueChangedEvent;

/**
 * Document generically implements the IDocument interface
 * and serves as a parent/ancestor to all generated and domain specific document base-classes.
 * It provides generic value access via it's getValue(s) and setValue(s) methods.
 */
abstract class Document implements IDocument, IValueChangedListener
{
    /**
     * Holds the document's module.
     *
     * @var IModule $module
     */
    private $module;

    /**
     * There is a IValueHolder instance for each IField of our module.
     * The '$value_holders' property maps fieldnames to their dedicated valueholder instance
     * and is used for lookups during setValue(s) invocations.
     *
     * @var ValueHolderMap $value_holders
     */
    private $value_holders;

    /**
     * Holds a list of all events that were received since the document was instanciated
     * or the 'markClean' method was called.
     *
     * @var array $changes
     */
    private $changes = array();

    /**
     * Holds all listeners that are notified about document changed.
     *
     * @var array $document_changed_listeners
     */
    private $document_changed_listeners = array();

    /**
     * Always holds the validation results for a prior setValue(s) invocation.
     * The results are held as a map where particular results can be accessed by fieldname.
     * There will be a result for every field affected by a setValue(s) call.
     *
     * @var ResultMap $validation_results
     */
    private $validation_results;

    /**
     * Create a document specific to the given module and hydrate it with the passed data.
     *
     * @param IModule $module
     * @param array $data
     */
    public function __construct(IModule $module, array $data = array())
    {
        $this->module = $module;
        // Setup a map of IValueHolder specific to our module's fields.
        // they hold the actual document data.
        $this->value_holders = new ValueHolderMap();
        foreach ($module->getFields() as $fieldname => $field) {
            $this->value_holders->setItem($fieldname, $field->createValueHolder());
        }
        // Hydrate initial data ...
        if (!empty($data)) {
            $this->setValues($data);
        }
        // ... then start tracking value-changed events.
        foreach ($this->value_holders as $value_holder) {
            $value_holder->addValueChangedListener($this);
        }
    }

    /**
     * Sets a specific value by fieldname.
     *
     * @param string $fieldname
     * @param mixed $value
     */
    public function setValue($fieldname, $value)
    {
        $this->validation_results = new ResultMap();
        $value_holder = $this->value_holders->getItem($fieldname);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for field: '" . $fieldname . "'. Invalid fieldname?"
            );
        }
        $this->validation_results->setItem($fieldname, $value_holder->setValue($value));

        return $this->isValid();
    }

    /**
     * Batch set a given list of field values.
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        // '$validation_results' is used to collect all particular validation results,
        // that are created each time we invoke '$this->setValue' for a given field value.
        $field_validation_results = new ResultMap();
        foreach ($this->module->getFields()->getKeys() as $fieldname) {
            if (array_key_exists($fieldname, $values)) {
                $this->setValue($fieldname, $values[$fieldname]);
                // memoize the current field validation result, after the prior call to setValue.
                $field_validation_results->setItem(
                    $fieldname,
                    $this->validation_results->getItem($fieldname)
                );
            }
        }
        $this->validation_results = $field_validation_results;

        return $this->isValid();
    }

    /**
     * Returns the value for a specific field.
     *
     * @param string $fieldname
     *
     * @return mixed
     */
    public function getValue($fieldname)
    {
        $value_holder = $this->value_holders->getItem($fieldname);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for field: '" . $fieldname . "'. Invalid fieldname?"
            );
        }

        return $value_holder->getValue();
    }

    /**
     * Tells if the document has a value set for a given field.
     *
     * @param string $fieldname
     *
     * @return boolean
     */
    public function hasValue($fieldname)
    {
        $value_holder = $this->value_holders->getItem($fieldname);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for field: '" . $fieldname . "'. Invalid fieldname?"
            );
        }

        return $value_holder->hasValue();
    }

    /**
     * Returns the values of all our fields or a just specific field subset,
     * that can be defined by the optional '$fieldnames' parameter.
     *
     * @param array $fieldnames
     *
     * @return array
     */
    public function getValues(array $fieldnames = array())
    {
        $values = array();

        if (!empty($fieldnames)) {
            foreach ($fieldnames as $fieldname) {
                $values[$fieldname] = $this->getValue($fieldname);
            }
        } else {
            foreach ($this->getModule()->getFields() as $field) {
                $values[$field->getName()] = $this->getValue($field->getName());
            }
        }

        return $values;
    }

    /**
     * Tells whether a spefic IDocument instance is considered equal to an other given document.
     * Documents are equal when they have both the same module and values.
     *
     * @param IDocument $document
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $document)
    {
        if ($document->getModule() !== $this->getModule()) {
            throw new BadValueException(
                "Only IDocument instances of the same module may be compared."
            );
        }

        $is_equal = true;
        foreach ($this->getModule()->getFields()->getKeys() as $fieldname) {
            $value_holder = $this->value_holders->getItem($fieldname);
            if (!$value_holder->isValueEqualTo($document->getValue($fieldname))) {
                $is_equal = false;
                break;
            }
        }

        return $is_equal;
    }

    /**
     * Returns the validation results of a prior call to setValue(s).
     * There will be a result for each affected field.
     *
     * @return ValidationMap
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
     * @return array
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
        return empty($this->changes);
    }

    /**
     * Marks the current document instance as clean, hence resets the all tracked changed.
     */
    public function markClean()
    {
        $this->changes = array();
    }

    /**
     * Returns the document's module.
     *
     * @return IModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Translates a given value-changed event into a corresponding document-changed event
     * and propagates the latter to all attached document-changed listeners.
     *
     * @param ValueChangedEvent $event
     */
    public function notifyDocumentChanged(ValueChangedEvent $event)
    {
        $event = DocumentChangedEvent::create($this, $event);
        foreach ($this->document_changed_listeners as $listener) {
            $listener->onDocumentChanged($event);
        }
    }

    /**
     * Attaches the given document-changed listener.
     *
     * @param IDocumentChangedListener $document_changed_listener
     */
    public function addDocumentChangedListener(IDocumentChangedListener $listener)
    {
        if (!in_array($listener, $this->document_changed_listeners)) {
            $this->document_changed_listeners[] = $listener;
        }
    }

    /**
     * Removes the given document-changed listener.
     *
     * @param IDocumentChangedListener $listener
     */
    public function removeDocumentChangedListener(IDocumentChangedListener $listener)
    {
        if (false !== ($pos = array_search($listener, $this->document_changed_listeners, true))) {
            array_splice($this->document_changed_listeners, $pos, 1);
        }
    }

    /**
     * Handles value-changed events that are received from the document's value holders.
     *
     * @param ValueChangedEvent $event
     */
    public function onValueChanged(ValueChangedEvent $event)
    {
        // @todo Possible optimization: only track events for RootModule documents,
        // what will save some memory when dealing with deeply nested aggregate structures.
        $this->changes[] = $event;
        $this->notifyDocumentChanged($event);
    }

    /**
     * Returns an array representation of a document's current value state.
     *
     * @return array
     */
    public function toArray()
    {
        $values = array();
        foreach ($this->getModule()->getFields() as $field) {
            $value = $this->getValue($field->getName());
            if ($field instanceof ReferenceField) {
                if (!empty($value)) {
                    $references = $field->getOption(ReferenceField::OPT_REFERENCES);
                    $identity_field = $references[0][ReferenceField::OPT_IDENTITY_FIELD];
                    $reference_identifiers = array();
                    foreach ($value as $document) {
                        $reference_identifiers[] = array(
                            'id' => $document->getValue($identity_field),
                            'module' => $document->getModule()->getName()
                        )
                    }
                    $values[$field->getName()] = $reference_identifiers;
                }
            } elseif ($value instanceof DocumentList) {
                $values[$field->getName()] = $value->toArray();
            } else {
                $values[$field->getName()] = $value;
            }
        }
        $values['@type'] = get_class($this);

        return $values;
    }
}
