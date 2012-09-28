<?php

namespace CMF\Core\Runtime\Module;

abstract class AggregateModule extends Module
{
    /**
     * Returns the default fields that are initially added to a module upon creation.
     *
     * @return array A list of CMF\Core\Runtime\Field\IField implemenations.
     */
    protected function getDefaultFields()
    {
        return array();
    }
}
