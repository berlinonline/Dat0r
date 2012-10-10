<?php

namespace Dat0r\Core\Runtime\Document;

/**
 * Represents excpetions that reflect occurences of invalid values
 * during method execution.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
interface IValueChangedListener
{
    /**
     * Handles value changed events received by emitters that we've registered to.
     *
     * @param ValueChangedEvent $event
     */
    public function onValueChanged(ValueChangedEvent $event);
}
