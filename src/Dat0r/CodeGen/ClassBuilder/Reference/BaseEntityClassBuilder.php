<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder as CommonBaseEntityClassBuilder;

class BaseEntityClassBuilder extends CommonBaseEntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference\\Base';
    }
}
