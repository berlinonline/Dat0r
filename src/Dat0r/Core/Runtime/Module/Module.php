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
     * Holds the field'S options.
     *
     * @var array $options
     */
    private $options = array();

    /** 
     * Returns the class(name) to use when creating new entries for this module.
     *
     * @return string
     */
    protected abstract function getDocumentImplementor();

    /**
     * Returns the pooled instance of a specific module.
     * Each module is pooled exactly once, making this a singleton style (factory)method.
     * This method is used to provide a convenient access to generated domain module instances.
     * 
     * @return IModule
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
     * Returns a module option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = NULL)
    {
        return $this->hasOption($name) ? $this->options[$name] : $default;
    }

    /**
     * Tells if a module currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
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
    protected function __construct($name, array $fields = array(), array $options = array())
    {
        $this->name = $name;
        $this->options = $options;
        
        $this->fields = FieldCollection::create($this->getDefaultFields());
        if (! empty($fields))
        {
            $this->fields->addMore($fields);
        }
    }

    protected function getDefaultFields()
    {
        return array();
    }
}
