<?php

namespace Dat0r\Core\Runtime\Document;

use Dat0r\Core\Runtime;

class DocumentChangedEvent implements Runtime\IEvent
{
    /**
     * @var Dat0r\Core\Runtime\Document\IDocument $document
     */
    private $document;

    /**
     * @var Dat0r\Core\Runtime\Document\ValueChangedEvent $valueChangedEvent
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

    protected function __construct(IDocument $document, ValueChangedEvent $valueChangedEvent)
    {
        $this->document = $document;
        $this->valueChangedEvent = $valueChangedEvent;
    }
}
