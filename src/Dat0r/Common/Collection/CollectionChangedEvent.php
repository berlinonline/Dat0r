<?php

namespace Dat0r\Common\Collection;

use Dat0r\Common\IEvent;
use Dat0r\Common\Object;

class CollectionChangedEvent extends Object implements IEvent
{
    const ITEM_ADDED = 'added';

    const ITEM_REMOVED = 'removed';

    protected $item;

    protected $type;

    public function __construct($item, $type)
    {
        $this->item = $item;
        $this->type = $type;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getType()
    {
        return $this->type;
    }
}
