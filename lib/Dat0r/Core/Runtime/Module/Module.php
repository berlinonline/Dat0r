<?php

namespace Dat0r\Core\Runtime\Module;

use Dat0r\Core\Runtime;
use Dat0r\Core\Runtime\Field;

/**
 * Base class that all Dat0r modules should extend.
 */
abstract class Module extends Runtime\Freezable implements IModule
{
    /**
     * @var array $instances Holds a list of IModule implementations stored by name.
     */
    private static $instances = array();

    /**
     * @var string $name Holds the module's name.
     */
    private $name;

    /**
     * @var FieldCollection $fields Holds the module's fields.
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
     *
     * @return Dat0r\Core\Runtime\Module\IModule
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
     * @return Dat0r\Core\Runtime\Field\FieldCollection
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

        $collection = Field\FieldCollection::create($fields);
        $collection->freeze();
        return $collection;
    }

    /**
     * Returns a certain module field by name.
     *
     * @param string $name
     *
     * @return Dat0r\Core\Runtime\Field\IField
     *
     * @throws Dat0r\Core\Runtime\Moule\InvalidFieldException
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
     * @return Dat0r\Core\Runtime\Document\IDocument
     *
     * @throws Dat0r\Core\Runtime\InvalidImplementorException
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
     * Closes the module to any further modifications.
     */
    public function freeze()
    {
        parent::freeze();
        $this->fields->freeze();
    }

    /**
     * Constructs a new Dat0r\Core\Runtime\Module\Module.
     *
     * @param string $name
     * @param array $fields
     */
    protected function __construct($name, array $fields)
    {
        $this->name = $name;

        $this->fields = Field\FieldCollection::create($this->getDefaultFields());
        $this->fields->addMore($fields);
    }
}
