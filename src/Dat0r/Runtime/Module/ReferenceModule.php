<?php

namespace Dat0r\Runtime\Module;

/**
 * Provides and contrains behaviour in the context of referencing other modules.
 */
abstract class ReferenceModule extends Module
{
    /**
     * Returns the default fields that are initially added to a module upon creation.
     *
     * @return array A list of IField implemenations.
     */
    protected function getDefaultFields()
    {
        return array();
    }
}
