<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder;

class BaseReferenceEntityClassBuilder extends BaseEntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference\\Base';
    }
}
