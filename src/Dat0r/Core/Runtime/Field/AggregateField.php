<?php

namespace Dat0r\Core\Runtime\Field;

use Dat0r\Core\Runtime\Module\AggregateModule;
use Dat0r\Core\Runtime\Error;

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
    const OPT_AGGREGATE_MODULE = 'aggregate_module';

    /**
     * Gets the aggregate module that has been set for the current field instance.
     *
     * @return AggregateModule
     */
    public function getAggregateModule()
    {
        if (! $this->hasOption(self::OPT_AGGREGATE_MODULE))
        {
            throw new Error\LogicException(
                "AggregateField instances must be provided an aggregate module option."
            );
        }
        $moduleImplementor = $this->getOption(self::OPT_AGGREGATE_MODULE);
        if (! class_exists($moduleImplementor))
        {
            throw new Error\InvalidImplementorException(
                "The module implementor: '$moduleImplementor' given to aggregate field: " . 
                $this->getName() . " can not be resolved."
            );
        }
        return $moduleImplementor::getInstance();
    }
}
