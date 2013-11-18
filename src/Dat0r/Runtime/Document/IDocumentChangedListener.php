<?php

namespace Dat0r\Runtime\Document;

/**
 * Represents a listener to events that occur, when a document instance changes one of it's values.
 */
interface IDocumentChangedListener
{
    /**
     * Handle document changed events.
     *
     * @param DocumentChangedEvent $event
     */
    public function onDocumentChanged(DocumentChangedEvent $event);
}
