<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Collection\TypedList;

class DocumentList extends TypedList
{
    public function toArray()
    {
        $data = array();

        foreach ($this->items as $document) {
            $data[] = $document->toArray();
        }

        return $data;
    }

    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Document\\IDocument';
    }
}
