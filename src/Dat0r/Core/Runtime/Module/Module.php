<?php

namespace Dat0r\Core\Runtime\Module;

use Dat0r\Core\Runtime\Freezable;
use Dat0r\Core\Runtime\Error;
use Dat0r\Core\Runtime\Document\IDocument;
use Dat0r\Core\Runtime\Field\IField;
use Dat0r\Core\Runtime\Field\FieldCollection;

/**
 * Base class that all Dat0r modules should extend.
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 * @author Thorsten Schmitt-Rink <tschmittrink@gmail.com>
 */
abstract class Module extends Freezable implements IModule
{
    /**
     * Holds a list of IModule implementations that are pooled by type.
     *
     * @var array $instances 
     */
    private static $instances = array();

    /**
     * Holds the module's name.
     *
     * @var string $name
     */
    private $name;

    /**
     * Holds the module's fields.
     *
     * @var FieldCollection $fields
     */
    private $fields;

    /** 
     * Returns the class(name) to use when creating new entries for this module.
     *
     * @return string
     */
    protected abstract function getDocumentImplementor();

    /**
     * Returns the pooled instance of a specific module.
     * Each module is pooled exactly once, making this a singleton style (factory)method.
     * This method is used to provide a convenient access to generated domain module instances,
     * which is the reason why this method takes no arguments.
     * 
     * @return IModule
     *
     * @codeCoverageIgnore
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (! isset(self::$instances[$class]))
        {
            $module = new static();
            $module->freeze();
            self::$instances[$class] = $module;
        }
        return self::$instances[$class];
    }

    /**
     * Standard factory method for dynamically creating modules.
     * During common usage this method probally ain't needed.
     * During testing it is, as it allows us to test modules dynamically. 
     *
     * @see ModuleTest.php
     *
     * @param string The name of the module to create.
     * @param array An array of IField implementations that define the module's structure.
     *
     * @return IModule
     */
    public static function create($name, array $fields)
    {
        return new static($name, $fields);
    }

    /**
     * Returns the name of the module.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

   /**
     * Returns the module's field collection.
     *
     * @param array $fieldnames A list of fieldnames to filter for.
     *
     * @return FieldCollection
     */
    public function getFields(array $fieldnames = array())
    {
        $fields = array();
        if (empty($fieldnames))
        {
            $fields = $this->fields->toArray();
        }
        else
        {
            foreach ($fieldnames as $fieldname)
            {
                $fields[$fieldname] = $this->getField($fieldname);
            }
        }

        $collection = FieldCollection::create($fields);
        $collection->freeze();
        return $collection;
    }

    /**
     * Returns a certain module field by name.
     *
     * @param string $name
     *
     * @return IField
     *
     * @throws InvalidFieldException
     */
    public function getField($name)
    {
        if (($field = $this->fields->get($name)))
        {
            return $field;
        }
        else
        {
            throw new InvalidFieldException("Module has no field: " . $name);
        }
    }

    /**
     * Creates a new IDocument instance.
     *
     * @param array $data Optional data for initial hydration.
     *
     * @return IDocument
     *
     * @throws Error\InvalidImplementorException
     */
    public function createDocument(array $data = array())
    {
        $implementor = $this->getDocumentImplementor();

        if (! class_exists($implementor, TRUE))
        {
            throw new Error\InvalidImplementorException(
                "Unable to resolve the given document implementor upon document creation request."
            );
        }
        // @todo maybe check interface before returning (ioc consistency check).
        return $implementor::create($this, $data);
    }

    /**
     * Freezes the module and all it's fields.
     */
    public function freeze()
    {
        parent::freeze();
        $this->fields->freeze();
    }

    /**
     * Constructs a new Module.
     *
     * @param string $name
     * @param array $fields
     */
    protected function __construct($name, array $fields)
    {
        $this->name = $name;

        $this->fields = FieldCollection::create($this->getDefaultFields());
        $this->fields->addMore($fields);
    }
}
