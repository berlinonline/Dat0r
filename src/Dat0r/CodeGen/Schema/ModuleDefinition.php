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

    public function getAggregateDefinitions(ModuleSchema $module_schema)
    {
        $aggregate_names = array();
        foreach ($this->fields as $field) {
            if ($field->getType() === 'aggregate') {
                foreach ($field->getOptions() as $option) {
                    if ($option->getName() === 'modules') {
                        $aggregate_names = array_merge(
                            $aggregate_names,
                            $option->getValue()->toArray()
                        );
                    }
                }
            }
        }

        $aggregates_set = ModuleDefinitionSet::create();
        foreach ($module_schema->getAggregateDefinitions() as $aggregate) {
            if (in_array($aggregate->getName(), $aggregate_names)) {
                $aggregates_set->add($aggregate);
            }
        }

        return $aggregates_set;
    }

    protected function __construct()
    {
        $this->fields = FieldDefinitionSet::create();
        $this->options = OptionDefinitionList::create();
    }
}
