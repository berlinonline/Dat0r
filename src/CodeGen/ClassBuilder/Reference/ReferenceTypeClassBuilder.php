<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\EntityTypeClassBuilder;

class ReferenceTypeClassBuilder extends EntityTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference';
    }

    protected function getNamespace()
    {
        return parent::getNamespace() . '\\Reference';
    }

    protected function getImplementor()
    {
        return $this->type_definition->getName() . ucfirst($this->config->getReferencedTypeSuffix('Type'));
    }
}
