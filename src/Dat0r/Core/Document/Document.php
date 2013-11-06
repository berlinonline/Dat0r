<?php

namespace Dat0r\Core\Document;

use Dat0r\Core\Error;
use Dat0r\Core\Module\IModule;
use Dat0r\Core\Field\ReferenceField;
use Dat0r\Core\Field\AggregateField;
use Dat0r\Core\ValueHolder\IValueHolder;
use Dat0r\Core\ValueHolder\ValueHolderCollection;

/**
 * Document completely implements the IDocument interface
 * and serves as the parent to generated domain specific Base\Document classes.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 *
 * @todo Add a marker interface for Repository integration.
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
     * @var ValueHolderCollection $values
     */
    private $values;

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
     * Sets a given list of values.
     *
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->hydrate($values);
    }

    /**
     * Sets a specific value by fieldname.
     *
     * @param string $fieldname
     * @param mixed $value
     */
    public function setValue($fieldname, $value)
    {
        $field = $this->module->getField($fieldname);

        if ($field->validate($value)) {
            $this->values->set($field, $value);
        } else {
            $error = new InvalidValueException();

            $error->setFieldname($fieldname);
            $error->setModuleName($this->module->getName());
            $error->setValue($value);

            throw $error;
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
        $field = $this->module->getField($fieldname);
        $value = null;

        if ($this->hasValue($fieldname)) {
            $value_holder = $this->values->get($field);
        } else {
            throw new InvalidValueException(
                "Field $fieldname has not been corretly initialized during initial hydrate."
            );
        }

        return ($raw === true) ? $value_holder->getValue() : $value_holder;
    }

    public function hasValue($fieldname)
    {
        $field = $this->module->getField($fieldname);
        return $this->values->has($field);
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
                                $index_fields = isset($reference_options['index_fields']) ? $reference_options['index_fields'] : array();
                                foreach ($index_fields as $index_fieldname) {
                                    $qualified_index_fieldname = $ref_module->getOption('prefix') . '.' . $index_fieldname;
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
            throw new Error\BadValueException(
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

    public function checkMandatoryFields()
    {
        foreach ($this->getModule()->getFields() as $field) {
            $value = $this->getValue($field->getName());
            $is_mandatory = ($field->getOption('mandatory') == true);

            if ($is_mandatory && empty($value)) {
                $error = new MandatoryValueMissingException();
                $error->setFieldName($field->getName());
                $error->setModuleName($this->module->getName());

                throw $error;
            }
        }
    }

    /**
     * Constructs a new Document instance.
     *
     * @param IModule $module
     * @param array $data
     */
    protected function __construct(IModule $module, array $data = array())
    {
        $this->module = $module;
        $this->values = ValueHolderCollection::create($this->getModule());
        $this->hydrate($data, true);
        $this->values->addValueChangedListener($this);
    }

    /**
     * Hydrates the given set of values into the current IDocument instance.
     *
     * @param array $values
     */
    protected function hydrate(array $values = array(), $apply_defaults = false)
    {
        $non_hydrated_fields =  array();

        if (!empty($values)) {
            foreach ($this->module->getFields() as $fieldname => $field) {
                if (array_key_exists($fieldname, $values)) {
                    $this->setValue($field->getName(), $values[$fieldname]);
                } else {
                    $non_hydrated_fields[] = $field;
                }
            }
        } else {
            foreach ($this->module->getFields() as $fieldname => $field) {
                $non_hydrated_fields[] = $field;
            }
        }

        if ($apply_defaults) {
            foreach ($non_hydrated_fields as $field) {
                $this->setValue($field->getName(), $field->getDefaultValue());
            }
        }
    }
}
