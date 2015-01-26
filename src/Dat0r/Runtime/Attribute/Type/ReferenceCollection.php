<?php

namespace Dat0r\Runtime\Attribute\Type;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\Validator\Rule\Type\ReferenceRule;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\Error\InvalidTypeException;

/**
 * ReferenceCollection allows to nest multiple types below a defined attribute_name.
 * Pass in the 'OPTION_MODULES' option to define the types you would like to nest.
 * The corresponding value-structure is organized as a collection of entities.
 *
 * Supported options: OPTION_MODULES
 */
class ReferenceCollection extends Attribute
{
    /**
     * Option that holds an array of supported reference-type names.
     */
    const OPTION_MODULES = 'references';

    /**
     * An array holding the reference-type instances supported by a specific reference-attribute instance.
     *
     * @var array
     */
    protected $referenced_types = null;

    /**
     * Constructs a new reference attribute instance.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = array())
    {
        parent::__construct($name, $options);

        foreach ($this->getReferences() as $reference_type) {
            foreach ($reference_type->getAttributes() as $attribute) {
                $attribute->setParent($this);
            }
        }
    }

    /**
     * Returns an reference-attribute instance's default value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return new EntityList();
    }

    /**
     * Returns the reference-types as an array.
     *
     * @return array
     */
    public function getReferences()
    {
        if (!$this->referenced_types) {
            $this->referenced_types = array();
            foreach ($this->getOption(self::OPTION_MODULES) as $reference_type) {
                $this->referenced_types[] = new $reference_type();
            }
        }

        return $this->referenced_types;
    }

    public function getReferenceByPrefix($prefix)
    {
        foreach ($this->getReferences() as $type) {
            if ($type->getPrefix() === $prefix) {
                return $type;
            }
        }

        return null;
    }

    public function getReferenceByName($name)
    {
        foreach ($this->getReferences() as $type) {
            if ($type->getName() === $name) {
                return $type;
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
            new ReferenceRule(
                'valid-data',
                array('reference_types' => $this->getReferences())
            )
        );

        return $rules;
    }
}
