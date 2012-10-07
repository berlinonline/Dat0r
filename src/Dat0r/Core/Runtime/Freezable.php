<?php

namespace Dat0r\Core\Runtime;

abstract class Freezable
{
    /**
     * @var boolean $frozen
     */
    private $frozen;

    /**
     * Closes a specific IFreezable instance for any further modifications.
     */
    public function freeze()
    {
        $this->frozen = TRUE;
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
