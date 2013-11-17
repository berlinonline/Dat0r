<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

class DocumentChangedListenerList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Document\\IDocumentChangedListener';
    }
}
