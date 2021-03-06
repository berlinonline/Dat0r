<?php

namespace Dat0r\Core\Module;

/**
 * Provides and contrains behaviour in the context of being nested by a another module.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
abstract class AggregateModule extends Module
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
