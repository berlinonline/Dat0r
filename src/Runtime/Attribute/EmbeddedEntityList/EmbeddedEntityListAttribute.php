<?php

namespace Dat0r\Runtime\Attribute\EmbeddedEntityList;

use Dat0r\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListRule;
use Dat0r\Runtime\Attribute\ListAttribute;
use Dat0r\Runtime\Entity\EntityList;
use Dat0r\Runtime\Validator\Rule\RuleList;
use Dat0r\Runtime\EntityTypeInterface;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\EntityTypeMap;

/**
 * Allows to nest multiple types below a defined attribute_name.
 * Pass in the 'OPTION_ENTITY_TYPES' option to define the types you would like to nest.
 * The corresponding value-structure is organized as a collection of entities.
 *
 * Supported options: OPTION_ENTITY_TYPES
 */
class EmbeddedEntityListAttribute extends ListAttribute
{
    /**
     * Option that holds an array of supported entity-type names.
     */
    const OPTION_ENTITY_TYPES = EmbeddedEntityListRule::OPTION_ENTITY_TYPES;

    /**
     * An array holding the embed-type instances supported by a specific embed-attribute instance.
     *
     * @var array
     */
    protected $entity_type_map = null;

    public function __construct(
        $name,
        EntityTypeInterface $type,
        array $options = [],
        AttributeInterface $parent = null
    ) {
        parent::__construct($name, $type, $options, $parent);

        $this->entity_type_map = new EntityTypeMap();
        foreach ($this->getOption(self::OPTION_ENTITY_TYPES) as $embedded_type_class) {
            if (!class_exists($embedded_type_class)) {
                throw new RuntimeException(
                    sprintf('Unable to load configured "embedded_entity_type" class called %s.', $embedded_type_class)
                );
            }
            $embedded_type = new $embedded_type_class($this->getType(), $this);
            $this->entity_type_map->setItem($embedded_type->getPrefix(), $embedded_type);
        }
    }

    /**
     * Returns an attribute's null value.
     *
     * @return mixed value to be used/interpreted as null (not set)
     */
    public function getNullValue()
    {
        return new EntityList();
    }

    public function getDefaultValue()
    {
        return $this->getNullValue();
    }

    /**
     * Returns the embed-types as an array.
     *
     * @return array
     */
    public function getEmbeddedEntityTypeMap()
    {
        return $this->entity_type_map;
    }

    public function getEmbeddedTypeByPrefix($prefix)
    {
        if ($this->getEmbeddedEntityTypeMap()->hasKey($prefix)) {
            return $this->getEmbeddedEntityTypeMap()->getItem($prefix);
        }

        return null;
    }

    public function getEmbeddedTypeByClassName($class_name)
    {
        $found_types = $this->getEmbeddedEntityTypeMap()->filter(
            function($entity_type) use ($class_name) {
                return get_class($entity_type) === $class_name;
            }
        )->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }

    public function getEmbeddedTypeByName($name)
    {
        $found_types = $this->getEmbeddedEntityTypeMap()->filter(
            function($entity_type) use ($name) {
                return $entity_type === $name;
            }
        )->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }

    /**
     * Return a list of rules used to validate a specific attribute instance's value.
     *
     * @return RuleList
     */
    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();
        $options[self::OPTION_ENTITY_TYPES] = $this->getEmbeddedEntityTypeMap();

        $rules->push(
            new EmbeddedEntityListRule('valid-embedded-entity-list-data', $options)
        );

        return $rules;
    }
}
