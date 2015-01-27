<?php

namespace Dat0r\Runtime;

use Dat0r\Common\Configurable;
use Dat0r\Common\OptionsInterface;
use Dat0r\Common\Error\InvalidTypeException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Entity\EntityInterface;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Attribute\Type\ReferenceCollection;
use Dat0r\Runtime\Attribute\AttributeInterface;
use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Attribute\AttributePath;

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
     * @param array $attribute_map
     * @param OptionsInterface $options
     */
    public function __construct($name, array $attribute_map = array(), OptionsInterface $options = null)
    {
        $this->name = $name;

        $this->options = $options;

        $this->attribute_map = new AttributeMap($this, $this->getDefaultAttributes());

        foreach ($attribute_map as $attribute) {
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
     * Sets the type's parent once, if it isn't yet assigned.
     *
     * @param EntityTypeInterface $parent
     */
    public function setParent(EntityTypeInterface $parent)
    {
        if (!$this->parent) {
            $this->parent = $parent;
        }
        // @todo else throw an exception,
        // as a second call to setParent might imply a logic error?
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
    public function getAttributes(array $attribute_names = array(), array $types = array())
    {
        $attribute_map = array();

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

        return new AttributeMap($this, $attribute_map);
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
    public function createEntity(array $data = array())
    {
        $implementor = $this->getEntityImplementor();

        if (!class_exists($implementor, true)) {
            throw new InvalidTypeException(
                "Unable to resolve the given entity implementor '$implementor' upon entity creation request."
            );
        }

        return new $implementor($this, $data);
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
        return array();
    }

    protected function getAttributeByPath($attribute_path)
    {
        return AttributePath::getAttributeByPath($this, $attribute_path);
    }
}