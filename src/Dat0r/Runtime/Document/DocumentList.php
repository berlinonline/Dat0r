<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\CollectionChangedEvent;

/**
 * DocumentList is a TypedList implementation, that holds IDocuments.
 * You can attach to it as an IDocumentChangedListener and will be notified
 * on all events occuring from it's contained documents.
 */
class DocumentList extends TypedList implements IDocumentChangedListener
{
    /**
     * Holds all currently attached document-changed listeners.
     *
     * @var DocumentChangedListenerList $listeners
     */
    protected $listeners = array();

    /**
     * Create/construct a new document list instance.
     */
    public function __construct(array $documents = array())
    {
        parent::__construct($documents);

        $this->listeners = new DocumentChangedListenerList();
    }

    /**
     * Attaches a given document-changed listener,
     * which will be notified about any changes on contained documents.
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
     * Detaches the given document-changed listener.
     *
     * @param IDocumentChangedListener $listener
     */
    public function removeDocumentChangedListener(IDocumentChangedListener $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->removeItem($listener);
        }
    }

    /**
     * Handles document-changed events that are sent by our aggregated documents.
     *
     * @param DocumentChangedEvent $event
     */
    public function onDocumentChanged(DocumentChangedEvent $event)
    {
        $this->propagateDocumentChangedEvent($event);
    }

    /**
     * Returns an array representation of the current document list.
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();

        foreach ($this->items as $document) {
            $data[] = $document->toArray();
        }

        return $data;
    }

    /**
     * Propagates a given document-changed event to all attached document-changed listeners.
     *
     * @param DocumentChangedEvent $event
     */
    protected function propagateDocumentChangedEvent(DocumentChangedEvent $event)
    {
        foreach ($this->listeners as $listener) {
            $listener->onDocumentChanged($event);
        }
    }

    /**
     * Propagates a given collection-changed event to all attached collection-changed listeners.
     *
     * @param CollectionChangedEvent $event
     */
    protected function propagateCollectionChangedEvent(CollectionChangedEvent $event)
    {
        if ($event->getType() === CollectionChangedEvent::ITEM_REMOVED) {
            $event->getItem()->removeDocumentChangedListener($this);
        } else {
            $event->getItem()->addDocumentChangedListener($this);
        }

        parent::propagateCollectionChangedEvent($event);
    }

    /**
     * Returns the IDocument interfacename to the TypeList parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Document\\IDocument';
    }
}
