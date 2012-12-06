<?php

namespace Dat0r\Core\Runtime\Document;

class DocumentSet implements \Countable, \ArrayAccess, \Iterator 
{
    private $documents;

    private $fieldname;

    public function __construct(array $documents = array())
    {
        $this->documents = $documents;
    }

    public function first()
    {
        $identitfiers = array_keys($this->documents);

        return $this->offsetGet($identitfiers[0]);
    }

    public function add(IDocument $document)
    {
        $identitfier = $this->getDocumentIdentifier($document);

        $this->offsetSet($identitfier, $document);
    }

    public function remove(IDocument $document)
    {
        $identitfier = $this->getDocumentIdentifier($document);

        $this->offsetUnset($offset);
    }

    public function toArray()
    {
        $data = array_map(function($document)
        {
            return $document->toArray();
        }, $this->documents);

        return $data;
    }

    public function count()
    {
        return count($this->documents);
    }

    public function offsetExists($offset)
    {
        return isset($this->documents[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->documents[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->documents[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        array_splice($this->documents, $offset, 1);
    }

    public function current()
    {
        if ($this->valid())
        {
            return current($this->documents);
        }
        else
        {
            return FALSE;
        }
    }

    public function key()
    {
        return key($this->documents);
    }

    public function next()
    {
        return next($this->documents);
    }

    public function rewind()
    {
        reset($this->documents);
    }

    public function valid()
    {
        return NULL !== key($this->documents);
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

        return $document->getValue($identifierField->getName());
    }
}
