<?php

namespace Dat0r\Common\Collection;

interface ListenerInterface
{
    public function onCollectionChanged(CollectionChangedEvent $event);
}
