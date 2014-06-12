<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\IEvent;
use Dat0r\Runtime\Attribute\ValueHolder\ValueChangedEvent;

/**
 * Represents an event that occurs when a document's value changes.
 * Document changes are triggered on a per attribute base.
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
     * @var ValueChangedEvent $value_changed_event
     */
    private $value_changed_event;

    /**
     * Creates a new document changed event instance.
     *
     * @param IDocument $document
     * @param ValueChangedEvent $value_changed_event
     *
     * @return DocumentChangedEvent
     */
    public static function create(IDocument $document, ValueChangedEvent $value_changed_event)
    {
        return new static($document, $value_changed_event);
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
        return $this->value_changed_event;
    }

     /**
     * Returns a string representation of the current event.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "[%s] A %s type's document attribute value has changed: \n %s",
            get_class($this),
            $this->getDocument()->getType()->getName(),
            $this->getValueChangedEvent()
        );
    }

    /**
     * Constructs a new DocumentChangedEvent instance.
     *
     * @param IDocument $document
     * @param ValueChangedEvent $value_changed_event
     */
    protected function __construct(IDocument $document, ValueChangedEvent $value_changed_event)
    {
        $this->document = $document;
        $this->value_changed_event = $value_changed_event;
    }
}
