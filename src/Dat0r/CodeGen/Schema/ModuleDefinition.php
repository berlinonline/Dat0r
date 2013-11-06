<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r;

class ModuleDefinition extends Dat0r\Object
{
    protected $name;

    protected $implementor;

    protected $document_implementor;

    protected $description;

    protected $options;

    protected $fields;

    public function __construct()
    {
        $this->fields = FieldDefinitionList::create();
        $this->options = OptionDefinitionList::create();
    }

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
}
