<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\EntityClassBuilder;

class ReferenceEntityClassBuilder extends EntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference';
    }
}
