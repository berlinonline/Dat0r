<?php
namespace Dat0r\Runtime;

use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Entity\EntityInterface;

/**
 * EntityTypeInterfaces define data structures by composing property related strategies named AttributeInterface,
 * to derive concrete instances of the defined data structures in form of EntityInterface's.
 */
interface EntityTypeInterface
{
    /**
     * Returns the name of the type.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the type's prefix (technical identifier).
     *
     * @return string
     */
    public function getPrefix();

    /**
     * Returns the type's parent, if it has one.
     *
     * @return EntityTypeInterface
     */
    public function getParent();

    /**
     * Sets the type's parent once, if it isn't yet assigned.
     *
     * @param EntityTypeInterface $parent
     */
    public function setParent(EntityTypeInterface $parent);

    /**
     * Returns the type's attribute map.
     *
     * @param array $attribute_names Optional list of attribute names to filter for.
     * @param array $types Optional list of attribute types to filter for.
     *
     * @return AttributeMap
     */
    public function getAttributes(array $attribute_names = array(), array $types = array());

    /**
     * Returns a certain type attribute by name.
     *
     * @param string $name
     *
     * @return AttributeInterface
     */
    public function getAttribute($name);

    /**
     * Creates a new EntityInterface instance.
     *
     * @param array $data Optional data for initial hydration.
     *
     * @return EntityInterface
     */
    public function createEntity(array $data = array());
}
