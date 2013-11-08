<?php

namespace Dat0r\Runtime\Field;

use Dat0r\Runtime\Module\AggregateModule;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;

class AggregateField extends Field
{
    /**
     * Holds the option name of the option that provides the module implementor that reflects
     * the aggregated data structure.
     */
    const OPT_MODULES = 'modules';

    protected $aggregated_modules = null;

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
        if ($this->aggregated_modules) {
            return $this->aggregated_modules;
        }

        if (!$this->hasOption(self::OPT_MODULES)) {
            throw new RuntimeException(
                "AggregateField instances must be provided an 'modules' option."
            );
        }

        $aggregated_modules = $this->getOption(self::OPT_MODULES);
        foreach ($aggregated_modules as $aggregated_module) {
            if (!class_exists($aggregated_module)) {
                throw new InvalidTypeException(
                    "Invalid implementor: '$aggregated_module' given to aggregate field."
                );
            }
            $this->aggregated_modules[] = $aggregated_module::getInstance();
        }

        return $this->aggregated_modules;
    }
}
