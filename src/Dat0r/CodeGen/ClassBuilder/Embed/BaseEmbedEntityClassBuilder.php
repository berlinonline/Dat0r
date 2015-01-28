<?php

namespace Dat0r\CodeGen\ClassBuilder\Embed;

use Dat0r\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder;

class BaseEmbedEntityClassBuilder extends BaseEntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Embed\\Base';
    }
}
