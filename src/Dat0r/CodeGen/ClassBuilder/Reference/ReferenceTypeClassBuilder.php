<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Embed\EmbedEntityTypeClassBuilder;

class ReferenceTypeClassBuilder extends EmbedEntityTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference';
    }

    protected function getNamespace()
    {
        return parent::getNamespace() . '\\Reference';
    }
}
