<?php

namespace Dat0r\Core\ValueHolder;

use Dat0r\Core\Error;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\AggregateField;
use Dat0r\Core\Document\DocumentCollection;
use Dat0r\Core\Document\IDocumentChangedListener;
use Dat0r\Core\Document\IAggregateChangedListener;
use Dat0r\Core\Document\DocumentChangedEvent;

/**
 * Default IValueHolder implementation used for holding nested documents.
 * Should be use as the base to all other dat0r valueholder implementations.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
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
    public function isGreaterThan(IValueHolder $other)
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
    public function isLessThan(IValueHolder $other)
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
    public function isEqualTo(IValueHolder $other)
    {
        $lefthand_docs = $this->getValue();
        $righthand_docs = $other->getValue();
        $equal = true;

        if (count($lefthand_docs) !== count($righthand_docs)) {
            $equal = false;
        } else {
            foreach ($lefthand_docs as $index => $document) {
                if ($index !== $righthand_docs->indexOf($document)) {
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
        $collection = new DocumentCollection($documents);
        parent::setValue($collection);
    }

    /**
     * Propagates a given document changed event to all corresponding listeners.
     *
     * @param DocumentChangedEvent $event
     */
    public function notifyAggregateChanged(DocumentChangedEvent $event)
    {
        foreach ($this->aggregate_changed_listeners as $listener) {
            $listener->onAggregateChanged($this->getField(), $event);
        }
    }

    /**
     * Registers a given listener as a recipient of aggregate changed events.
     *
     * @param IAggregateChangedListener $aggregate_changed_listener
     */
    public function addAggregateChangedListener(IAggregateChangedListener $aggregate_changed_listener)
    {
        if (!in_array($aggregate_changed_listener, $this->aggregate_changed_listeners)) {
            $this->aggregate_changed_listeners[] = $aggregate_changed_listener;
        }
    }

    /**
     * Handles document changed events that are sent by our aggregated document.
     *
     * @param DocumentChangedEvent $event
     */
    public function onDocumentChanged(DocumentChangedEvent $event)
    {
        $this->notifyAggregateChanged($event);
    }

    /**
     * Contructs a new AggregateValueHolder instance from a given value.
     *
     * @param IField $field
     * @param mixed $value
     */
    protected function __construct(IField $field, $value = null)
    {
        if (!($field instanceof AggregateField)) {
            throw new Error\BadValueException(
                "Only instances of AggregateField my be associated with AggregateValueHolder."
            );
        }

        parent::__construct($field, $value);
    }
}
