<?php
namespace Dat0r\Runtime\Module;

use Dat0r\Runtime\Attribute\IAttribute;
use Dat0r\Runtime\Attribute\AttributeMap;
use Dat0r\Runtime\Document\IDocument;

/**
 * IModules define data structures by composing property related strategies named IAttribute,
 * to derive concrete instances of the defined data structures in form of IDocument's.
 */
interface IModule
{
    /**
     * Returns the name of the module.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the module's parent, if it has one.
     *
     * @return IModule
     */
    public function getParent();

    /**
     * Sets the module's parent once, if it isn't yet assigned.
     *
     * @param IModule $parent
     */
    public function setParent(IModule $parent);

    /**
     * Returns the module's attribute map.
     *
     * @param array $attribute_names Optional list of attribute names to filter for.
     * @param array $types Optional list of attribute types to filter for.
     *
     * @return AttributeMap
     */
    public function getAttributes(array $attribute_names = array(), array $types = array());

    /**
     * Returns a certain module attribute by name.
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
