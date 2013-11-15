<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\CollectionChangedEvent;

class DocumentList extends TypedList implements IDocumentChangedListener
{
    protected $document_changed_listeners = array();

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
     * Handles document changed events that are sent by our aggregated document.
     *
     * @param DocumentChangedEvent $event
     */
    public function onDocumentChanged(DocumentChangedEvent $event)
    {
        $this->propagateDocumentChangedEvent($event);
    }

    public function toArray()
    {
        $data = array();

        foreach ($this->items as $document) {
            $data[] = $document->toArray();
        }

        return $data;
    }

    /**
     * Propagates a given document changed event to all corresponding listeners.
     *
     * @param DocumentChangedEvent $event
     */
    protected function propagateDocumentChangedEvent(DocumentChangedEvent $event)
    {
        foreach ($this->document_changed_listeners as $listener) {
            $listener->onDocumentChanged($event);
        }
    }

    protected function propagateCollectionChangedEvent(CollectionChangedEvent $event)
    {
        if ($event->getType() === CollectionChangedEvent::ITEM_REMOVED) {
            $event->getItem()->removeDocumentChangedListener($this);
        } else {
            $event->getItem()->addDocumentChangedListener($this);
        }

        parent::propagateCollectionChangedEvent($event);
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Document\\IDocument';
    }
}
