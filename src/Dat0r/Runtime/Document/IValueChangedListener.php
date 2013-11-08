<?php

namespace Dat0r\Runtime\Document;

interface IValueChangedListener
{
    /**
     * Handle value changed events received by emitters that we've registered to.
     *
     * @param ValueChangedEvent $event
     */
    public function onValueChanged(ValueChangedEvent $event);
}
