<?php

namespace Dat0r\Core\Field;

use Dat0r\Core\Module\AggregateModule;
use Dat0r\Core\Error;

/**
 * Concrete implementation of the Field base class.
 * Stuff in here is dedicated to handling aggregates (nested data structures).
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
class AggregateField extends Field
{
    /**
     * Holds the option name of the option that provides the module implementor that reflects
     * the aggregated data structure.
     */
    const OPT_MODULES = 'modules';

    protected $aggregated_modules = array();

    public function getDefaultValue()
    {
        return array();
    }

    /**
     * Gets the aggregate module that has been set for the current field instance.
     *
     * @return AggregateModule
     */
    public function getAggregateModules()
    {
        if (!$this->hasOption(self::OPT_MODULES)) {
            throw new Error\LogicException(
                "AggregateField instances must be provided an 'modules' option."
            );
        }

        $aggregated_modules = $this->getOption(self::OPT_MODULES);
        foreach ($aggregated_modules as $aggregated_module) {
            if (! class_exists($aggregated_module)) {
                throw new Error\InvalidImplementorException(
                    "Invalid implementor: '$aggregated_module' given to aggregate field."
                );
            }
            $this->aggregated_modules[] = $aggregated_module::getInstance();
        }

        return $this->aggregated_modules;
    }
}
