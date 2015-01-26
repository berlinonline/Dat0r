<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\Common\EntityClassBuilder as CommonEntityClassBuilder;

class EntityClassBuilder extends CommonEntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Aggregate';
    }
}
