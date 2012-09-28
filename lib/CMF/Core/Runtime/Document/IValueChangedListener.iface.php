<?php

namespace CMF\Core\Runtime\Document;

interface IValueChangedListener
{
    public function onValueChanged(ValueChangedEvent $event);
}
