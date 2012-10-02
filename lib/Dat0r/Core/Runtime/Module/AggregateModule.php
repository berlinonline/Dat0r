<?php

namespace Dat0r\Core\Runtime\Module;

abstract class AggregateModule extends Module
{
    /**
     * Returns the default fields that are initially added to a module upon creation.
     *
     * @return array A list of Dat0r\Core\Runtime\Field\IField implemenations.
     */
    protected function getDefaultFields()
    {
        return array();
    }
}
