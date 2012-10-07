<?php
namespace Dat0r\Core\Runtime\Module;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\Field;
use Dat0r\Core\Runtime\Document;

/**
 * @todo write a meaningfull text that explains what modules are and what they do.
 */
interface IModule extends Runtime\IFreezable
{
    /**
     * Gets a module's pooled instance.
     *
     * @return IModule
     */
    public static function getInstance();

    /**
     * Returns the name of the module.
     * 
     * @return string
     */
    public function getName();

    /**
     * Returns the module's field collection.
     *
     * @param array $fieldnames A list of fieldnames to filter for.
     *
     * @return Field\FieldCollection
     */
    public function getFields(array $fieldnames = array());

    /**
     * Returns a certain module field by name.
     *
     * @param string $name
     *
     * @return Field\IField
     */
    public function getField($name);

    /**
     * Creates a new IDocument instance.
     *
     * @param array $data Optional data for initial hydration.
     *
     * @return Document\IDocument
     */
    public function createDocument(array $data = array());
}
