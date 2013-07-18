<?php

namespace Dat0r\CodeGen\Schema;

class ModuleDefinition extends BaseDefinition
{
    protected $name;

    protected $implementor;

    protected $document_implementor;

    protected $description;

    protected $options = array();

    protected $fields;

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

    protected function __construct()
    {
        $this->fields = new FieldDefinitionSet();
        $this->options = new OptionDefinitionList();
    }
}
