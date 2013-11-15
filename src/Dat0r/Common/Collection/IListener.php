<?php

namespace Dat0r\Common\Collection;

interface IListener
{
    public function onCollectionChanged(CollectionChangedEvent $event);
}
