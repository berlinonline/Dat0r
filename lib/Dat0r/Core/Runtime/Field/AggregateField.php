<?php

namespace Dat0r\Core\Runtime\Field;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\Error;

/**
 * Concreate implementation of the Field base class.
 * Stuff in here is dedicated to handling aggregates (nested data structures).
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
     * @return Dat0r\Core\Runtime\Module\AggregateModule
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

    /**
     * Returns the IValueHolder implementation to use when aggregating (value)data for this field.
     *
     * @return string Fully qualified name of an IValueHolder implementation.
     */
    protected function getValueHolderImplementor()
    {
        return 'Dat0r\Core\Runtime\ValueHolder\AggregateValueHolder';
    }

    /**
     * Returns the IValidator implementation to use when validating values for this field.
     *
     * @return string Fully qualified name of an IValidator implementation.
     */
    protected function getValidationImplementor()
    {
        return 'Dat0r\Core\Runtime\Validator\AggregateValidator';
    }
}
