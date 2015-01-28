<?php

namespace Dat0r\CodeGen\ClassBuilder\Embed;

use Dat0r\CodeGen\ClassBuilder\Common\EntityClassBuilder;

class EmbedEntityClassBuilder extends EntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Embed';
    }
}
