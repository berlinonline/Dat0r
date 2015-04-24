<?php

namespace Dat0r\Runtime;

use Dat0r\Common\Configurable;
use Dat0r\Common\Error\InvalidTypeException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Common\OptionsInterface;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Attribute\AttributePath;
use Dat0r\Runtime\Entity\EntityInterface;

/**
 * Base class that all Dat0r types should extend.
 */
abstract class EntityType extends Configurable implements EntityTypeInterface
{
    /**
     * Holds the type's name.
     *
     * @var string $name
     */
    protected $name;

    /**
     * Holds a reference to the parent type, if there is one.
     *
     * @var EntityTypeInterface $parent;
     */
    protected $parent;

    /**
     * Holds a reference to the parent attribute, if there is one.
     *
     * @var AttributeInterface $parent_attribute;
     */
    protected $parent_attribute;

    /**
     * Holds the type's attribute map.
     *
     * @var AttributeMap $attribute_map
     */
    protected $attribute_map;

    /**
     * Holds the type's prefix.
     *
     * @var string $prefix
     */
    protected $prefix;

    /**
     * Holds the type's options.
     *
     * @var Options $options
     */
    protected $options;

    /**
     * Returns the class(name) to use when creating new entries for this type.
     *
     * @return string
     */
    abstract protected function getEntityImplementor();

    /**
     * Constructs a new Type.
     *
     * @param string $name
     * @param array $attributes
     * @param OptionsInterface $options
     * @param EntityTypeInterface $parent
     * @param AttributeInterface $parent_attribute
     */
    public function __construct(
        $name,
        array $attributes = [],
        OptionsInterface $options = null,
        EntityTypeInterface $parent = null,
        AttributeInterface $parent_attribute = null
    ) {
        if ($parent xor $parent_attribute) {
            throw new RuntimeException('Parent and parent-attribute must be set together. Only one given.');
        }

        parent::__construct([ 'options' => $options ]);

        $this->name = $name;
        $this->parent = $parent;
        $this->parent_attribute = $parent_attribute;

        $this->attribute_map = new AttributeMap($this->getDefaultAttributes());
        foreach ($attributes as $attribute) {
            $this->attribute_map->setItem($attribute->getName(), $attribute);
        }
    }

    public function getEntityType()
    {
        return $this->getEntityImplementor();
    }

    /**
     * Returns the name of the type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the type's parent, if it has one.
     *
     * @return EntityTypeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the type's parent-attribute, if it has one.
     *
     * @return AttributeInterface
     */
    public function getParentAttribute()
    {
        return $this->parent_attribute;
    }

    /**
     * Returns the type's prefix (techn. identifier).
     *
     * @return string
     */
    public function getPrefix()
    {
        if (!$this->prefix) {
            if (ctype_lower($this->getName())) {
                $this->prefix = $this->getName();
            } else {
                $this->prefix = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $this->getName()));
            }
        }

        return $this->prefix;
    }

    /**
     * Returns the type's attribute collection.
     *
     * @param array $attribute_names A list of attribute_names to filter for.
     *
     * @return AttributeMap
     */
    public function getAttributes(array $attribute_names = [], array $types = [])
    {
        $attribute_map = [];

        if (empty($attribute_names)) {
            $attribute_map = $this->attribute_map->toArray();
        } else {
            foreach ($attribute_names as $attribute_name) {
                $attribute_map[$attribute_name] = $this->getAttribute($attribute_name);
            }
        }

        if (!empty($types)) {
            $attribute_map = array_filter(
                $attribute_map,
                function ($attribute) use ($types) {
                    return in_array(get_class($attribute), $types);
                }
            );
        }

        return new AttributeMap($attribute_map);
    }

    /**
     * Returns a certain type attribute by name.
     *
     * @param string $name
     *
     * @return AttributeInterface
     *
     * @throws RuntimeException
     */
    public function getAttribute($name)
    {
        if (mb_strpos($name, '.')) {
            return $this->getAttributeByPath($name);
        }
        // @todo use $this->attribute_map->hasKey($name) instead
        if (($attribute = $this->attribute_map->getItem($name))) {
            return $attribute;
        } else {
            throw new RuntimeException("Type has no attribute: " . $name);
        }
    }

    /**
     * Creates a new EntityInterface instance.
     *
     * @param array $data Optional data for initial hydration.
     *
     * @return EntityInterface
     *
     * @throws InvalidTypeException
     */
    public function createEntity(array $data = [], EntityInterface $parent_entity = null)
    {
        $implementor = $this->getEntityImplementor();

        if (!class_exists($implementor, true)) {
            throw new InvalidTypeException(
                "Unable to resolve the given entity implementor '$implementor' upon entity creation request."
            );
        }

        return new $implementor($this, $data, $parent_entity);
    }

    public function getDefaultAttributeNames()
    {
        return array_keys($this->getDefaultAttributes());
    }

    /**
     * @todo All entities should have a uuid field by default
     */
    protected function getDefaultAttributes()
    {
        return [];
    }

    protected function getAttributeByPath($attribute_path)
    {
        return AttributePath::getAttributeByPath($this, $attribute_path);
    }
}
