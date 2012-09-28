<?php

namespace CMF\Core\Runtime;

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
