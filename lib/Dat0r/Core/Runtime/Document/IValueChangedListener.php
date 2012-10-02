<?php

namespace Dat0r\Core\Runtime\Document;

interface IValueChangedListener
{
    public function onValueChanged(ValueChangedEvent $event);
}
