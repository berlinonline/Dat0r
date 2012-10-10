<?php

namespace Dat0r\Core\Runtime\Module;

use Dat0r\Core\Runtime\Field\TextField;

/**
 * Provides behaviour in the context of being a top-level (aggregate root) module.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
abstract class RootModule extends Module
{
    /**
     * Returns the default fields that are initially added to a module upon creation.
     *
     * @return array A list of IField implemenations.
     */
    protected function getDefaultFields()
    {
        return array(
            TextField::create('id'),
            TextField::create('revision')
        );
    }
}
