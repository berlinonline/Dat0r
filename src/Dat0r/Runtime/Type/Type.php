<?php

namespace Dat0r\Runtime\Type;

use Dat0r\Common\Object;
use Dat0r\Common\Error\InvalidTypeException;
use Dat0r\Common\Error\RuntimeException;
use Dat0r\Runtime\Document\IDocument;
use Dat0r\Runtime\Attribute\Type\AggregateCollection;
use Dat0r\Runtime\Attribute\Type\ReferenceCollection;
use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Attribute\AttributePath;

/**
 * Base class that all Dat0r types should extend.
 */
abstract class Type extends Object implements IType
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
     * @var IType $parent;
     */
    protected $parent;

    /**
     * Holds the type's attribute map.
     *
     * @var AttributeMap $attribute_map
     */
    protected $attribute_map;

    /**
     * Holds the type's options.
     *
     * @var array $options
     */
    protected $options = array();

    /**
     * Holds the type's prefix.
     *
     * @var string $prefix
     */
    protected $prefix;

    /**
     * Returns the class(name) to use when creating new entries for this type.
     *
     * @return string
     */
    abstract protected function getDocumentImplementor();

    /**
     * Constructs a new Type.
     *
     * @param string $name
     * @param array $attribute_map
     */
    public function __construct($name, array $attribute_map = array(), array $options = array())
    {
        $this->name = $name;
        $this->options = $options;

        $this->attribute_map = new AttributeMap($this, $this->getDefaultAttributes());

        foreach ($attribute_map as $attribute) {
            $this->attribute_map->setItem($attribute->getName(), $attribute);
        }
    }

    public function getDocumentType()
    {
        return $this->getDocumentImplementor();
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
     * @return IType
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the type's parent once, if it isn't yet assigned.
     *
     * @param IType $parent
     */
    public function setParent(IType $parent)
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
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $this->getName(), $match_results);
            $matches = $match_results[0];
            foreach ($matches as &$match) {
                $match = ($match == strtoupper($match)) ? strtolower($match) : lcfirst($match);
            }
            $this->prefix = implode('_', $matches);
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
     * @return IAttribute
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
     * Creates a new IDocument instance.
     *
     * @param array $data Optional data for initial hydration.
     *
     * @return IDocument
     *
     * @throws InvalidTypeException
     */
    public function createDocument(array $data = array())
    {
        $implementor = $this->getDocumentImplementor();

        if (!class_exists($implementor, true)) {
            throw new InvalidTypeException(
                "Unable to resolve the given document implementor upon document creation request."
            );
        }

        return new $implementor($this, $data);
    }

    /**
     * Returns a type option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return $this->hasOption($name) ? $this->options[$name] : $default;
    }

    /**
     * Tells if a type currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getDefaultAttributeNames()
    {
        return array_keys($this->getDefaultAttributes());
    }

    protected function getDefaultAttributes()
    {
        return array();
    }

    protected function getAttributeByPath($attribute_path)
    {
        return AttributePath::getAttributeByPath($this, $attribute_path);
    }
}
