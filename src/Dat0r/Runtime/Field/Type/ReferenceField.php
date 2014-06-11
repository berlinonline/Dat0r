<?php

namespace Dat0r\Runtime\Field\Type;

use Dat0r\Runtime\Field\Field;
use Dat0r\Runtime\Document\DocumentList;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\ReferenceRule;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;

/**
 * ReferenceField allows to nest multiple modules below a defined fieldname.
 * Pass in the 'OPTION_MODULES' option to define the modules you would like to nest.
 * The corresponding value-structure is organized as a collection of documents.
 *
 * Supported options: OPTION_MODULES
 */
class ReferenceField extends Field
{
    /**
     * Option that holds an array of supported reference-module names.
     */
    const OPTION_MODULES = 'references';

    /**
     * An array holding the reference-module instances supported by a specific reference-field instance.
     *
     * @var array
     */
    protected $referenced_modules = null;

    /**
     * Constructs a new reference field instance.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = array())
    {
        parent::__construct($name, $options);

        foreach ($this->getReferenceModules() as $reference_module) {
            foreach ($reference_module->getFields() as $field) {
                $field->setParent($this);
            }
        }
    }

    /**
     * Returns an reference-field instance's default value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return DocumentList::create();
    }

    /**
     * Returns the reference-modules as an array.
     *
     * @return array
     */
    public function getReferenceModules()
    {
        if (!$this->referenced_modules) {
            $this->referenced_modules = array();
            foreach ($this->getOption(self::OPTION_MODULES) as $reference_module) {
                $this->referenced_modules[] = new $reference_module();
            }
        }

        return $this->referenced_modules;
    }

    public function getReferenceModuleByPrefix($prefix)
    {
        foreach ($this->getReferenceModules() as $module) {
            if ($module->getPrefix() === $prefix) {
                return $module;
            }
        }

        return null;
    }

    public function getReferenceModuleByName($name)
    {
        foreach ($this->getReferenceModules() as $module) {
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
            new ReferenceRule(
                'valid-data',
                array('reference_modules' => $this->getReferenceModules())
            )
        );

        return $rules;
    }
}
