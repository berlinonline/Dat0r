<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Validation\Result\IIncident;
use Dat0r\Runtime\Validation\Service as ValidationService;
use Dat0r\Runtime\Module\IModule;
use Dat0r\Runtime\Field\Type\ReferenceField;
use Dat0r\Runtime\Field\Type\AggregateField;
use Dat0r\Runtime\ValueHolder\IValueHolder;
use Dat0r\Runtime\ValueHolder\ValueHolderMap;
use Dat0r\Runtime\ValueHolder\IValueChangedListener;
use Dat0r\Runtime\ValueHolder\ValueChangedEvent;

/**
 * Document completely implements the IDocument interface
 * and serves as the parent to generated domain specific Base\Document classes.
 */
abstract class Document implements IDocument, IValueChangedListener
{
    /**
     * Holds the documents parent module.
     *
     * @var IModule $module
     */
    private $module;

    /**
     * Represents a list of value holders that (surprise) hold a document's values.
     *
     * @var ValueHolderMap $value_holders
     */
    private $value_holders;

    /**
     * Holds a list of IEvent (ValueChangedEvent or DocumentChangedEvent).
     *
     * @var array $changes
     */
    private $changes = array();

    /**
     * Holds a list of listeners regisered to our document changed event.
     *
     * @var array $document_changed_listeners
     */
    private $document_changed_listeners = array();

    /**
     * Creates a new Document.
     *
     * @param IModule $module
     * @param array $data
     *
     * @return IDocument
     */
    public static function create(IModule $module, array $data = array())
    {
        return new static($module, $data);
    }

    /**
     * Constructs a new Document instance.
     *
     * @param IModule $module
     * @param array $data
     */
    public function __construct(IModule $module, array $data = array())
    {
        $this->module = $module;

        $this->value_holders = new ValueHolderMap();
        foreach ($module->getFields() as $fieldname => $field) {
            $value_holder = $field->createValueHolder();
            $value_holder->setValue($field->getDefaultValue());
            $this->value_holders->setItem($fieldname, $value_holder);
        }

        if (!empty($data)) {
            $this->setValues($data);
        }

        foreach ($this->value_holders as $value_holder) {
            $value_holder->addValueChangedListener($this);
        }
    }

    /**
     * Sets a given list of values.
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($this->module->getFields()->getKeys() as $fieldname) {
            if (array_key_exists($fieldname, $values)) {
                $this->setValue($fieldname, $values[$fieldname]);
            }
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
        $value_holder = $this->value_holders->getItem($fieldname);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for field: '" . $fieldname . "'. Invalid fieldname?"
            );
        }

        $result = $value_holder->setValue($value);
        if ($result->getSeverity() > IIncident::SUCCESS) {
            foreach ($result->getViolatedRules() as $violated_rule) {
                foreach ($violated_rule->getIncidents() as $name => $incident) {
                    // @todo Do something smart with the error information here.
                    // Mark the document as invalid and return false.
                }
            }
        }
    }

    /**
     * Returns the value for a specific field.
     *
     * @param string $fieldname
     * @param boolean $raw Whether to return the raw value or the corresponding IValueHolder instance.
     *
     * @return IValueHolder
     */
    public function getValue($fieldname, $raw = true)
    {
        $value_holder = $this->value_holders->getItem($fieldname);
        if (!$value_holder) {
            throw new RuntimeException(
                "Unable to find IValueHolder for field: '" . $fieldname . "'. Invalid fieldname?"
            );
        }

        return ($raw === true) ? $value_holder->getValue() : $value_holder;
    }

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
     * Returns the values of either all fields or a specific field subset
     * defined by the optional fieldnames parameter.
     *
     * @param array $fieldnames
     * @param boolean $raw Whether to return the raw value or the corresponding IValueHolder instance.
     *
     * @return array A list of IValueHolder or raw values depending on the $raw flag.
     */
    public function getValues(array $fieldnames = array(), $raw = true)
    {
        $values = array();

        if (!empty($fieldnames)) {
            foreach ($fieldnames as $fieldname) {
                $values[$fieldname] = $this->getValue($fieldname, $raw);
            }
        } else {
            foreach ($this->getModule()->getFields() as $field) {
                $values[$field->getName()] = $this->getValue($field->getName(), $raw);
            }
        }

        return $values;
    }

    /**
     * Returns an array representation of an entries current value state.
     *
     * @return array
     */
    public function toArray()
    {
        $values = array();
        foreach ($this->getModule()->getFields() as $field) {
            $value = $this->getValue($field->getName());
            if ($field instanceof ReferenceField) {
                if (! empty($value)) {
                    $refMap = array();
                    $references = $field->getOption(ReferenceField::OPT_REFERENCES);
                    $identity_field = $references[0][ReferenceField::OPT_IDENTITY_FIELD];
                    $reference_identifiers = array();
                    foreach ($value as $document) {
                        $ref_module = $document->getModule();
                        $ref_data = array(
                            'id' => $document->getValue($identity_field),
                            'module' => $ref_module->getOption('prefix', strtolower($ref_module->getName()))
                        );
                        foreach ($field->getOption('references') as $reference_options) {
                            if ($reference_options['module'] === '\\' . get_class($ref_module)) {
                                $index_fields = isset($reference_options['index_fields'])
                                    ? $reference_options['index_fields']
                                    : array();
                                foreach ($index_fields as $index_fieldname) {
                                    $qualified_index_fieldname = $ref_module->getOption('prefix').'.'.$index_fieldname;
                                    $ref_data[$qualified_index_fieldname] = $document->getValue($index_fieldname);
                                }
                            }
                        }
                        $reference_identifiers[] = $ref_data;
                    }
                    $values[$field->getName()] = $reference_identifiers;
                }
            } elseif ($field instanceof AggregateField) {
                if ($value instanceof DocumentList) {
                    $values[$field->getName()] = $value->toArray();
                }
            } else {
                $values[$field->getName()] = $value;
            }
        }
        $values['type'] = get_class($this);

        return $values;
    }

    /**
     * Returns a list of unhandled changes.
     *
     * @return array An list of ValueChangedEvent.
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
     * Marks the current document instance as clean,
     * hence resets the all tracked changed.
     */
    public function markClean()
    {
        $this->changes = array();
    }

    /**
     * Returns an entries module.
     *
     * @return IModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Tells whether a spefic IDocument instance is considered equal to an other given IDocument.
     *
     * @param IDocument $other
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $other)
    {
        $is_equal = true;

        if ($other->getModule() !== $this->getModule()) {
            throw new BadValueException(
                "Only IDocument instances of the same module may be compared."
            );
        }

        foreach ($this->getModule()->getFields() as $field) {
            $lefthand_value = $this->getValue($field->getName(), false);
            $righthand_value = $other->getValue($field->getName(), false);
            if (!$lefthand_value->isEqualTo($righthand_value)) {
                $is_equal = false;
                break;
            }
        }

        return $is_equal;
    }

    /**
     * Propgates the given value changed event
     * as a document changed event to our registered document changed listeners.
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
     * Registers a given document changed listener.
     *
     * @param IDocumentChangedListener $document_changed_listener
     */
    public function addDocumentChangedListener(IDocumentChangedListener $document_changed_listener)
    {
        if (!in_array($document_changed_listener, $this->document_changed_listeners)) {
            $this->document_changed_listeners[] = $document_changed_listener;
        }
    }

    /**
     * Handles value changed events that are received from our value holders.
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
}
