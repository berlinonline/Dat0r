<?php
namespace Dat0r\Core\Module;

use Dat0r\Core\IFreezable;
use Dat0r\Core\Field\IField;
use Dat0r\Core\Field\FieldCollection;
use Dat0r\Core\Document\IDocument;

/**
 * IModules define data structures by composing property related strategies named IField,
 * to derive concrete instances of the defined data structures in form of IDocument's.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
interface IModule extends IFreezable
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
     * @return FieldCollection
     */
    public function getFields(array $fieldnames = array(), $types = array());

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

    /**
     * Returns the field to use when identitfying documents
     * that belong to a module.
     *
     * @return IField
     */
    public function getIdentifierField();
}
