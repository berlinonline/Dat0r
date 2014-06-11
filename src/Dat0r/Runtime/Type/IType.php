<?php
namespace Dat0r\Runtime\Type;

use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Document\IDocument;

/**
 * ITypes define data structures by composing property related strategies named IAttribute,
 * to derive concrete instances of the defined data structures in form of IDocument's.
 */
interface IType
{
    /**
     * Returns the name of the type.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the type's parent, if it has one.
     *
     * @return IType
     */
    public function getParent();

    /**
     * Sets the type's parent once, if it isn't yet assigned.
     *
     * @param IType $parent
     */
    public function setParent(IType $parent);

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
     * @return IAttribute
     */
    public function getAttribute($name);

    /**
     * Creates a new IDocument instance.
     *
     * @param array $data Optional data for initial hydration.
     *
     * @return IDocument
     */
    public function createDocument(array $data = array());
}
