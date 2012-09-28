<?php

namespace CMF\Core\Runtime\Document;

use CMF\Core\Runtime;

class DocumentChangedEvent implements Runtime\IEvent
{
    /**
     * @var CMF\Core\Runtime\Document\IDocument $document
     */
    private $document;

    /**
     * @var CMF\Core\Runtime\Document\ValueChangedEvent $valueChangedEvent
     */
    private $valueChangedEvent;

    public static function create(IDocument $document, ValueChangedEvent $valueChangedEvent)
    {
        return new static($document, $valueChangedEvent);
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getValueChangedEvent()
    {
        return $this->valueChangedEvent;
    }

    protected function __construct(IDocument $document, ValueChangedEvent $valueChangedEvent)
    {
        $this->document = $document;
        $this->valueChangedEvent = $valueChangedEvent;
    }
}
