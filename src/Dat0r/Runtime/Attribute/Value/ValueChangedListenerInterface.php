<?php

namespace Dat0r\Runtime\Attribute\Value;

interface ValueChangedListenerInterface
{
    /**
     * Handle value changed events received by emitters that we've registered to.
     *
     * @param ValueChangedEvent $event
     */
    public function onValueChanged(ValueChangedEvent $event);
}
