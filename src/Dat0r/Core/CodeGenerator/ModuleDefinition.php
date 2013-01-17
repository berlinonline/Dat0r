<?php

namespace Dat0r\Core\CodeGenerator;

class ModuleDefinition
{
    private $package;

    private $root;

    private $namespace;

    private $type;

    private $baseModule;

    private $baseDocument;

    private $name;

    private $description;

    private $options = array();

    private $fields = array();

    private $aggregates = array();

    public static function create(array $data = array())
    {
        return new static($data);
    }

    public function getPackage()
    {
        return $this->package;
    }

    public function getBaseModule()
    {
        return $this->baseModule;
    }

    public function getBaseDocument()
    {
        return $this->baseDocument;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getAggregates()
    {
        return $this->aggregates;
    }

    public function setAggregates(array $aggregates)
    {
        $this->aggregates = $aggregates;
    }

    public function getRoot()
    {
        return $this->root;
    }

    protected function __construct(array $data)
    {
        $requiredData = array(
            'package', 'type', 'root', 'baseModule', 'baseDocument',
            'name', 'description', 'options', 'fields', 'aggregates', 'namespace'
        );

        foreach ($requiredData as $prop)
        {
            if (isset($data[$prop]))
            {
                $this->$prop = $data[$prop];
            }
            else
            {
                throw new \Exception("Missing key $prop in data given to " . __CLASS__);
            }
        }
    }
}
