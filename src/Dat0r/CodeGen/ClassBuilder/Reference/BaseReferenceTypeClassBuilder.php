<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Embed\BaseEmbedEntityTypeClassBuilder;

class BaseReferenceTypeClassBuilder extends BaseEmbedEntityTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference\\Base';
    }

    protected function getNamespace()
    {
        return $this->type_schema->getNamespace() . '\\Reference\\Base';
    }
}
