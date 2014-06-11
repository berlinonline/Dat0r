<?php
namespace Dat0r\Runtime\Module;

use Dat0r\Runtime\Field\IField;
use Dat0r\Runtime\Field\FieldMap;
use Dat0r\Runtime\Document\IDocument;

/**
 * IModules define data structures by composing property related strategies named IField,
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
     * Returns the module's field collection.
     *
     * @param array $fieldnames A list of fieldnames to filter for.
     *
     * @return FieldMap
     */
    public function getFields(array $fieldnames = array(), array $types = array());

    /**
     * Returns a certain module field by name.
     *
     * @param string $name
     *
     * @return IField
     */
    public function getField($name);

    /**
     * Creates a new IDocument instance.
     *
     * @param array $data Optional data for initial hydration.
     *
     * @return IDocument
     */
    public function createDocument(array $data = array());
}
