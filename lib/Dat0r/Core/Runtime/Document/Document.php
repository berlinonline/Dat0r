<?php

namespace Dat0r\Core\Runtime\Document;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\Error;
use Dat0r\Core\Runtime\Module;
use Dat0r\Core\Runtime\ValueHolder;

/**
 * Base class that all Dat0r entries should extend.
 * @todo Add an 'extends BaseDocument' for IRepository integration.
 */
abstract class Document implements IDocument, IValueChangedListener 
{
    /**
     * @var Dat0r\Core\Runtime\Module\IModule $module
     */
    private $module;

    /**
     * @var Dat0r\Core\Runtime\ValueHolder\ValueHolderCollection $values
     */
    private $values;

    /**
     * @var array $changes Holds a list of IEvent (ValueChangedEvent or DocumentChangedEvent).
     */
    private $changes = array();

    private $documentChangedListeners = array();

    /**
     * Creates a new Document.
     *
     * @param Dat0r\Core\Runtime\Module\IModule $module
     * @param array $data
     *
     * @return Dat0r\Core\Runtime\Document\IDocument 
     */
    public static function create(Module\IModule $module, array $data = array())
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

        if ($field->validate($value))
        {
            $this->values->set($field, $value);
        }
        else
        {
            throw new InvalidValueException(
                sprintf("Invalid field value given for field: %s", $fieldname)
            );
        }
    }

    /**
     * Returns the value for a specific field.
     *
     * @param string $fieldname
     * @param boolean $raw Whether to return the raw value or the corresponding IValueHolder instance.
     *
     * @return Dat0r\Core\Runtime\ValueHolder\IValueHolder
     */
    public function getValue($fieldname, $raw = TRUE)
    {
        $field = $this->module->getField($fieldname);
        $value = NULL;
        if ($this->values->has($field))
        {
            $valueHolder = $this->values->get($field);
        }
        else
        {
            $valueHolder = $field->getDefaultValue();
        }
        return (TRUE === $raw) ? $valueHolder->getValue() : $valueHolder;
    }

    /**
     * Returns the values of either all fields or a specific field subset
     * defined by the optional fieldnames parameter.
     *
     * @param array $fieldnames
     * @param boolean $raw Whether to return the raw value or the corresponding IValueHolder instance.
     *
     * @return array A list of Dat0r\Core\Runtime\ValueHolder\IValueHolder.
     */
    public function getValues(array $fieldnames = array(), $raw = TRUE)
    {
        $values = array();
        if (! empty($fieldnames))
        {
            foreach ($fieldnames as $fieldname)
            {
                $values[$fieldname] = $this->getValue($fieldname, $raw);
            }
        }
        else
        {
            foreach ($this->getModule()->getFields() as $field)
            {
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
        foreach ($this->getModule()->getFields() as $field)
        {
            $value = $this->getValue($field->getName());
            if (is_object($value) && is_callable(array($value, 'toArray')))
            {
                $values[$field->getName()] = $value->toArray();
            }
            else if (is_scalar($value))
            {
                $values[$field->getName()] = $value;
            }
            else
            {
                // @todo Someone messed it up, throw Core\LogicException?
            }
        }
        return $values;
    }

    /**
     * Returns a list of unhandled changes.
     * 
     * @return array An list of Dat0r\Core\Runtime\Document\ValueChangedEvent.
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
     * @return Dat0r\Core\Runtime\Module\IModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /** 
     * Tells whether a spefic IDocument instance is considered equal to an other given IDocument.
     *
     * @param Dat0r\Core\Runtime\Document\IDocument $other
     *
     * @return boolean
     */
    public function isEqualTo(IDocument $other)
    {
        if ($other->getModule() !== $this->getModule())
        {
            throw new Error\BadValueException(
                "Only IDocument instances of the same module may be compared."
            );
        }

        $isEqual = TRUE;
        foreach ($this->getModule()->getFields() as $field)
        {
            $lValueHolder = $this->getValue($field->getName(), FALSE);
            $rValueHolder = $other->getValue($field->getName(), FALSE);
            if (! $lValueHolder->isEqualTo($rValueHolder))
            {
                $isEqual = FALSE;
                break;
            }
        }
        return $isEqual;
    }

    public function notifyDocumentChanged(ValueChangedEvent $event)
    {
        $event = DocumentChangedEvent::create($this, $event);
        foreach ($this->documentChangedListeners as $listener)
        {
            $listener->onDocumentChanged($event);
        }
    }

    public function addDocumentChangedListener(IDocumentChangedListener $documentChangedListener)
    {
        if (! in_array($documentChangedListener, $this->documentChangedListeners))
        {
            $this->documentChangedListeners[] = $documentChangedListener;
        }
    }

    public function onValueChanged(ValueChangedEvent $event)
    {
        // @todo Possible optimization: only track events for RootModule documents,
        // what will save some memory when dealing with deeply nested aggregate structures.
        $this->changes[] = $event;
        $this->notifyDocumentChanged($event);
    }

    /**
     * Constructs a new Document instance.
     *
     * @param Dat0r\Core\Runtime\Module\IModule $module
     * @param array $data
     */
    protected function __construct(Module\IModule $module, array $data = array())
    {
        $this->module = $module;
        $this->values = ValueHolder\ValueHolderCollection::create($this->getModule());
        $this->hydrate($data);
        $this->values->addValueChangedListener($this);
    }

    /**
     * Hydrates the given set of values into the current IDocument instance.
     *
     * @param array $values
     */
    protected function hydrate(array $values = array())
    {
        if (! empty($values))
        {
            foreach ($this->module->getFields() as $fieldname => $field)
            {
                if (array_key_exists($fieldname, $values))
                {
                    $this->setValue($field->getName(), $values[$fieldname]);
                }
            }
        }
    }
}
