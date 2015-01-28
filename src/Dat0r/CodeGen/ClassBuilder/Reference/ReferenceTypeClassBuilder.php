<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\EntityTypeClassBuilder;

class ReferenceTypeClassBuilder extends EntityTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference';
    }
}
