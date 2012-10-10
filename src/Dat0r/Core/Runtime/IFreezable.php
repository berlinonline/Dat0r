<?php

namespace Dat0r\Core\Runtime;

/**
 * The IFreezable interface reflects an object's ability of being frozen,
 * Being 'frozen' refers to a state in which an object is expected to act as immutable.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
interface IFreezable
{
    /**
     * Closes a concrete object instance for further modifications.
     */
    public function freeze();

    /**
     * Tells whether a concrete object instance is frozen or not. 
     *
     * @return boolean
     */
    public function isFrozen();
}
