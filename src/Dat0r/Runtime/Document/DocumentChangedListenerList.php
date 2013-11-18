<?php

namespace Dat0r\Runtime\Document;

use Dat0r\Common\Collection\TypedList;
use Dat0r\Common\Collection\IUniqueCollection;

/**
 * Represents a list of document-changed listeners.
 */
class DocumentChangedListenerList extends TypedList implements IUniqueCollection
{
    /**
     * Returns the IDocumentChangedListener interface-name to the TypeList parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Runtime\\Document\\IDocumentChangedListener';
    }
}
