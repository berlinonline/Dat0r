<?php

namespace Dat0r\CodeGen\ClassBuilder\Embed;

use Dat0r\CodeGen\ClassBuilder\Common\EntityTypeClassBuilder;

class EmbedTypeClassBuilder extends EntityTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Embed';
    }
}
