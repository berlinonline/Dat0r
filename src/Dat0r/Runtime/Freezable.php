<?php

namespace Dat0r\Runtime;

/**
 * Freezable provides a base implementation of the IFreezable interface.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
abstract class Freezable
{
    /**
     * Holds a flag telling if the current instance is considered frozen or not.
     *
     * @var boolean $frozen
     */
    private $frozen;

    /**
     * Closes a specific IFreezable instance for any further modifications.
     */
    public function freeze()
    {
        $this->frozen = true;
    }

    /**
     * Tells whether a specific IFreezable instance is frozen or not.
     *
     * @return boolean
     */
    public function isFrozen()
    {
        return $this->frozen;
    }
}
