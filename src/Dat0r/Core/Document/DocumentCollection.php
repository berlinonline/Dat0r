<?php

namespace Dat0r\Core\Document;

use Dat0r\TypedList;

class DocumentCollection extends TypedList
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
        return '\\Dat0r\\Core\\Document\\IDocument';
    }
}
