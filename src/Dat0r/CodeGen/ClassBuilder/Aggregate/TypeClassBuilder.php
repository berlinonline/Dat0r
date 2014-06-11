<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\Common\TypeClassBuilder as CommonTypeClassBuilder;

class TypeClassBuilder extends CommonTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Aggregate';
    }
}
