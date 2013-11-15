<?php

namespace Dat0r\Common\Collection;

class ListenerList extends TypedList implements IUniqueCollection
{
    protected function getItemImplementor()
    {
        return '\\Dat0r\\Common\\Collection\\IListener';
    }
}
