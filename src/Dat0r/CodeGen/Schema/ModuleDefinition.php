<?php

namespace Dat0r\CodeGen\Schema;

class ModuleDefinition
{
    private $name;

    private $implementor;

    private $document_implementor;

    private $description;

    private $options = array();

    private $fields;

    public function getName()
    {
        return $this->name;
    }

    public function getImplementor()
    {
        return $this->implementor;
    }

    public function getDocumentImplementor()
    {
        return $this->document_implementor;
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

    public function toArray()
    {
        return array(
            'name' => $this->name,
            'implementor' => $this->implementor,
            'document_implementor' => $this->document_implementor,
            'description' => $this->description,
            'options' => $this->options->toArray(),
            'fields' => $this->fields->toArray()
        );
    }

    public static function create(array $data = array())
    {
        $module_definition = new static();

        foreach ($data as $key => $value)
        {
            if (property_exists($module_definition, $key))
            {
                $module_definition->$key = $value;
            }
        }

        return $module_definition;
    }

    protected function __construct()
    {
        $this->fields = new FieldDefinitionSet();
        $this->options = new OptionDefinitionList();
    }
}
