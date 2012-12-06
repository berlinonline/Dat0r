<?php

namespace Dat0r\Core\Runtime\Document;

class DocumentCollection implements \Countable, \ArrayAccess, \Iterator
{
    private $documents;

    public function __construct(array $documents = array())
    {
        $this->documents = $documents;
    }

    public function first()
    {
        return 1 <= $this->count() ? $this->documents[0] : FALSE;
    }

    public function add(IDocument $document)
    {
        $this->documents[] = $document;
    }

    public function remove(IDocument $document)
    {
        $offset = array_search($document, $this->documents, TRUE);

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
}
