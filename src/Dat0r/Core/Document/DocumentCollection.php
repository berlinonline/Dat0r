<?php

namespace Dat0r\Core\Document;

class DocumentCollection implements \Countable, \ArrayAccess, \Iterator
{
    private $documents;

    public function __construct(array $documents = array())
    {
        $this->documents = $documents;
    }

    public function indexOf(IDocument $document)
    {
        return array_search($document, $this->documents, true);
    }

    public function first()
    {
        return ($this->count() >= 1) ? $this->documents[0] : false;
    }

    public function add(IDocument $document)
    {
        $this->documents[] = $document;
    }

    public function remove(IDocument $document)
    {
        $this->offsetUnset($this->indexOf($document));
    }

    public function toArray()
    {
        $data = array_map(
            function ($document) {
                return $document->toArray();
            },
            $this->documents
        );

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
        if ($this->valid()) {
            return current($this->documents);
        } else {
            return false;
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
        return (key($this->documents) !== null);
    }
}
