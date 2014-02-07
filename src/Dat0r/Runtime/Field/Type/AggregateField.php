<?php

namespace Dat0r\Runtime\Field\Type;

use Dat0r\Runtime\Field\Field;
use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\AggregateRule;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;

/**
 * AggregateField allows to nest multiple modules below a defined fieldname.
 * Pass in the 'OPTION_MODULES' option to define the modules you would like to nest.
 * The corresponding value-structure is organized as a collection of documents.
 *
 * Supported options: OPTION_MODULES
 */
class AggregateField extends Field
{
    /**
     * Option that holds an array of supported aggregate-module names.
     */
    const OPTION_MODULES = 'modules';

    /**
     * An array holding the aggregate-module instances supported by a specific aggregate-field instance.
     *
     * @var array
     */
    protected $aggregated_modules = null;

    /**
     * Returns an aggregate-field instance's default value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return DocumentList::create();
    }

    /**
     * Returns the aggregate-modules as an array.
     *
     * @return array
     */
    public function getAggregateModules()
    {
        if (!$this->aggregated_modules) {
            $this->aggregated_modules = array();
            foreach ($this->getOption(self::OPTION_MODULES) as $aggregate_module) {
                $this->aggregated_modules[] = $aggregate_module::getInstance();
            }
        }

        return $this->aggregated_modules;
    }

    public function getAggregateModuleByPrefix($prefix)
    {
        foreach ($this->getAggregateModules() as $module) {
            if ($module->getPrefix() === $prefix) {
                return $module;
            }
        }

        return null;
    }

    public function getAggregateModuleByName($name)
    {
        foreach ($this->getAggregateModules() as $module) {
            if ($module->getName() === $name) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Return a list of rules used to validate a specific field instance's value.
     *
     * @return RuleList
     */
    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(
            new AggregateRule(
                'valid-data',
                array('aggregate_modules' => $this->getAggregateModules())
            )
        );

        return $rules;
    }
}
