<?php

namespace Dat0r\Core\Runtime\Document;

use Dat0r\Core\Runtime\IEvent;

/**
 * Represents an event that occurs when a document's value changes.
 * Document changes are triggered on a per field base.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class DocumentChangedEvent implements IEvent
{
    /**
     * Holds a reference to the document instance that changed.
     *
     * @var IDocument $document
     */
    private $document;

    /**
     * Holds the value changed event that reflects our change origin.
     *
     * @var ValueChangedEvent $valueChangedEvent
     */
    private $valueChangedEvent;

    /**
     * Creates a new document changed event instance.
     *
     * @param IDocument $document
     * @param ValueChangedEvent $valueChangedEvent
     *
     * @return DocumentChangedEvent
     */
    public static function create(IDocument $document, ValueChangedEvent $valueChangedEvent)
    {
        return new static($document, $valueChangedEvent);
    }

    /**
     * Returns the affected document.
     *
     * @return IDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Returns the value changed origin event.
     *
     * @return ValueChangedEvent
     */
    public function getValueChangedEvent()
    {
        return $this->valueChangedEvent;
    }

     /**
     * Returns a string representation of the current event.
     *
     * @return string
     */
    public function __toString()
    {
        $stringRep = sprintf(
            "[%s] A %s module's document field value has changed: \n %s",
            get_class($this),
            $this->getDocument()->getModule()->getName(),
            $this->getValueChangedEvent()
        );

        return $stringRep;
    }

    /**
     * Constructs a new DocumentChangedEvent instance.
     *
     * @param IDocument $document
     * @param ValueChangedEvent $valueChangedEvent
     */
    protected function __construct(IDocument $document, ValueChangedEvent $valueChangedEvent)
    {
        $this->document = $document;
        $this->valueChangedEvent = $valueChangedEvent;
    }
}
