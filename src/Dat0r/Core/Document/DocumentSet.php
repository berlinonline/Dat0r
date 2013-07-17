<?php

namespace Dat0r\Core\Document;

class DocumentSet extends DocumentCollection
{
    private $ids;

    public function __construct(array $documents = array())
    {
        parent::__construct($documents);

        $this->ids = array();

        foreach ($this as $document)
        {
            $this->ids[] = $this->getDocumentIdentifier($document);
        }
    }

    public function add(IDocument $document)
    {
        $identitfier = $this->getDocumentIdentifier($document);

        if (! in_array($identitfier, $this->ids))
        {
            parent::add($document);
        }
    }

    public function remove(IDocument $document)
    {
        parent::remove($document);

        array_splice($this->ids, $this->indexOf($document), 1);
    }

    /**
     * @todo We could either do as we are doing now and leave it to the module config
     * to define the field to be used here, allowing for documents of different modules
     * to dynamically align with each other, by the danger of very divers data or x-field collusions.
     *
     * Or we could give the DocumentSet a idenitieferName property or so, that would be used among all 
     * documents being passed in.
     */
    protected function getDocumentIdentifier(IDocument $document)
    {
        $identifierField = $document->getModule()->getIdentifierField();

        return sprintf(
            '%s.%s',
            $document->getModule()->getName(),
            $document->getValue($identifierField->getName())
        );
    }
}
