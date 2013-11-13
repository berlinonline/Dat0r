<?php

namespace Dat0r\Runtime\ValueHolder;

use Dat0r\Common\Error\BadValueException;
use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\Type\AggregateField;
use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Document\IDocumentChangedListener;
use Dat0r\Runtime\Document\DocumentChangedEvent;

/**
 * Default IValueHolder implementation used for holding nested documents.
 * Should be use as the base to all other dat0r valueholder implementations.
 */
class AggregateValueHolder extends ValueHolder implements IDocumentChangedListener
{
    /**
     * Holds a list of listeners to our aggregate changed event.
     *
     * @var array $aggregate_changed_listeners
     */
    private $aggregate_changed_listeners = array();

    /**
     * Tells whether a spefic IValueHolder instance's value is considered greater than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isGreaterThan($righthand_value)
    {
        return false;
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered less than
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isLessThan($righthand_value)
    {
        return false;
    }

    /**
     * Tells whether a spefic IValueHolder instance's value is considered equal to
     * the value of an other given IValueHolder.
     *
     * @param IValueHolder $other
     *
     * @return boolean
     */
    public function isEqualTo($righthand_value)
    {
        $lefthand_docs = $this->getValue();
        $equal = true;

        if (count($lefthand_docs) !== count($righthand_value)) {
            $equal = false;
        } else {
            foreach ($lefthand_docs as $index => $document) {
                if ($index !== $righthand_value->indexOf($document)) {
                    $equal = false;
                }
            }
        }

        return $equal;
    }

    /**
     * Sets the value holder's (array) value.
     *
     * @param array $value
     */
    public function setValue($value)
    {
        $collection = null;
        // @todo move to validator
        if ($value instanceof DocumentList) {
            $collection = $value;
        } elseif (null === $value) {
            $collection = array();
        } elseif (is_array($value)) {
            $module_map = array();
            ksort($value);

            foreach ($this->getField()->getAggregateModules() as $module) {
                $module_map[$module->getDocumentType()] = $module;
            }

            $documents = array();
            foreach ($value as $document_data) {
                if (!isset($document_data['type'])) {
                    continue;
                    //throw new Exception("Missing type information for aggregate data.");
                }
                $aggregate_type = $document_data['type'];
                if ($aggregate_type{0} !== '\\') {
                    $aggregate_type = '\\' . $aggregate_type;
                }

                if (!isset($module_map[$aggregate_type])) {
                    continue;
                    //throw new Exception("Unable to find related module for aggregate data.");
                }

                $aggregate_module = $module_map[$aggregate_type];
                $aggregate_document = $aggregate_module->createDocument($document_data);
                $aggregate_document->addDocumentChangedListener($this);
                $documents[] = $aggregate_document;
            }

            $collection = new DocumentList($documents);
        } else {
            throw new InvalidValueException(
                'Only DocumentLists or arrays of document data or null are acceptable values for AggregateFields.'
            );
        }

        return parent::setValue($collection);
    }

    public function addDocument($document)
    {
        $document_collection = $this->getValue();
        $document->addDocumentChangedListener($this);
        $document_collection->add($document);
    }

    /**
     * Handles document changed events that are sent by our aggregated document.
     *
     * @param DocumentChangedEvent $event
     */
    public function onDocumentChanged(DocumentChangedEvent $event)
    {
        $value_changed_event = $event->getValueChangedEvent();

        $this->propagateValueChangedEvent(
            ValueChangedEvent::create(
                $value_changed_event->getField(),
                $value_changed_event->getOldValue(),
                $value_changed_event->getNewValue(),
                $event
            )
        );
    }
}
