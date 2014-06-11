<?php

namespace Dat0r\Runtime\Attribute\Type;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\AggregateRule;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;

/**
 * AggregateCollection allows to nest multiple modules below a defined attribute_name.
 * Pass in the 'OPTION_MODULES' option to define the modules you would like to nest.
 * The corresponding value-structure is organized as a collection of documents.
 *
 * Supported options: OPTION_MODULES
 */
class AggregateCollection extends Attribute
{
    /**
     * Option that holds an array of supported aggregate-module names.
     */
    const OPTION_MODULES = 'modules';

    /**
     * An array holding the aggregate-module instances supported by a specific aggregate-attribute instance.
     *
     * @var array
     */
    protected $aggregated_modules = null;

    /**
     * Constructs a new aggregate attribute instance.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = array())
    {
        parent::__construct($name, $options);

        foreach ($this->getAggregateModules() as $aggregate_module) {
            foreach ($aggregate_module->getAttributes() as $attribute) {
                $attribute->setParent($this);
            }
        }
    }

    /**
     * Returns an aggregate-attribute instance's default value.
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
                $this->aggregated_modules[] = new $aggregate_module();
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
     * Return a list of rules used to validate a specific attribute instance's value.
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
