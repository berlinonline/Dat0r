<?php

namespace Dat0r\Runtime\Attribute\EmbeddedEntityList;

use Dat0r\Runtime\Attribute\Attribute;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\Validator\Rule\RuleList;

/**
 * Allows to nest multiple types below a defined attribute_name.
 * Pass in the 'OPTION_ENTITY_TYPES' option to define the types you would like to nest.
 * The corresponding value-structure is organized as a collection of entities.
 *
 * Supported options: OPTION_ENTITY_TYPES
 */
class EmbeddedEntityListAttribute extends Attribute
{
    /**
     * Option that holds an array of supported entity-type names.
     */
    const OPTION_ENTITY_TYPES = 'entity_types';

    /**
     * An array holding the embed-type instances supported by a specific embed-attribute instance.
     *
     * @var array
     */
    protected $entity_types = null;

    /**
     * Constructs a new embed attribute instance.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = array())
    {
        parent::__construct($name, $options);

        foreach ($this->getEntityTypes() as $embed_type) {
            foreach ($embed_type->getAttributes() as $attribute) {
                $attribute->setParent($this);
            }
        }
    }

    /**
     * Returns an embed-attribute instance's default value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return new EntityList();
    }

    /**
     * Returns the embed-types as an array.
     *
     * @return array
     */
    public function getEntityTypes()
    {
        if (!$this->entity_types) {
            $this->entity_types = array();
            foreach ($this->getOption(self::OPTION_ENTITY_TYPES) as $embed_type) {
                $this->entity_types[] = new $embed_type();
            }
        }

        return $this->entity_types;
    }

    public function getEmbedTypeByPrefix($prefix)
    {
        foreach ($this->getEntityTypes() as $type) {
            if ($type->getPrefix() === $prefix) {
                return $type;
            }
        }

        return null;
    }

    public function getEmbedTypeByName($name)
    {
        foreach ($this->getEntityTypes() as $type) {
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
            new EmbeddedEntityListRule(
                'valid-data',
                array('entity_types' => $this->getEntityTypes())
            )
        );

        return $rules;
    }
}
