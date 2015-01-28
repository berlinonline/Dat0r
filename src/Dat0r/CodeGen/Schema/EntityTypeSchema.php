<?php

namespace Dat0r\CodeGen\Schema;

use Dat0r\Common\Object;

class EntityTypeSchema extends Object
{
    protected $self_uri;

    protected $namespace;

    protected $package;

    protected $type_definition;

    protected $embed_definitions;

    protected $reference_definitions;

    public function __construct(array $state = array())
    {
        $this->type_definition = new EntityTypeDefinition();
        $this->embed_definitions = new EntityTypeDefinitionList();
        $this->reference_definitions = new EntityTypeDefinitionList();

        parent::__construct($state);
    }

    public function getSelfUri()
    {
        return $this->self_uri;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getEntityTypeDefinition()
    {
        return $this->type_definition;
    }

    public function setEntityTypeDefinition(EntityTypeDefinition $type_definition)
    {
        $this->type_definition = $type_definition;

        if (!$this->package) {
            $namespace_parts = explode('\\', $this->namespace);
            $this->package = end($namespace_parts);
        }
    }

    public function getEmbedDefinitions(array $names = array())
    {
        if (empty($names)) {
            return $this->embed_definitions;
        }

        $embed_types = array();
        foreach ($this->embed_definitions as $embed_type) {
            if (in_array($embed_type->getName(), $names)) {
                $embed_types[] = $embed_type;
            }
        }

        return $embed_types;
    }

    public function getUsedEmbedDefinitions(EntityTypeDefinition $type_definition)
    {
        $embed_types_definitions_list = new EntityTypeDefinitionList();
        $embed_type_attributes = $type_definition->getAttributes()->filterByType('embedded-entity-list');

        foreach ($embed_type_attributes as $embed_type_attribute) {
            $embed_typed_types_opt = $embed_type_attribute->getOptions()->filterByName('entity_types');
            $embed_types = $this->getEmbedDefinitions($embed_typed_types_opt->getValue()->toArray());

            foreach ($embed_types as $embed_type) {
                $embed_types_definitions_list->addItem($embed_type);

                foreach ($this->getUsedEmbedDefinitions($embed_type) as $nested_embed_type) {
                    $embed_types_definitions_list->addItem($nested_embed_type);
                }
            }
        }

        return $embed_types_definitions_list;
    }

    public function getReferenceDefinitions(array $names = array())
    {
        if (empty($names)) {
            return $this->reference_definitions;
        }

        $references = array();
        foreach ($this->reference_definitions as $reference) {
            if (in_array($reference->getName(), $names)) {
                $references[] = $reference;
            }
        }

        return $references;
    }

    public function getUsedReferenceDefinitions(EntityTypeDefinition $type_definition)
    {
        $reference_definitions_list = new EntityTypeDefinitionList();
        $reference_attributes = $type_definition->getAttributes()->filterByType('entity-reference-list');

        foreach ($reference_attributes as $reference_attribute) {
            $references_option = $reference_attribute->getOptions()->filterByName('entity_types');
            $references = $this->getReferenceDefinitions($references_option->getValue()->toArray());

            foreach ($references as $reference) {
                $reference_definitions_list->addItem($reference);
            }
        }

        foreach ($this->getUsedEmbedDefinitions($type_definition) as $embed_type) {
            foreach ($this->getUsedReferenceDefinitions($embed_type) as $reference) {
                $reference_definitions_list->addItem($reference);
            }
        }

        return $reference_definitions_list;
    }

    public function getPackage()
    {
        return $this->package;
    }
}
