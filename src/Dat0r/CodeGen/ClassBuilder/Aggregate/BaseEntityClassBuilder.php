<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder as CommonBaseEntityClassBuilder;

class BaseEntityClassBuilder extends CommonBaseEntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Aggregate\\Base';
    }
}
