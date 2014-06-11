<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\Common\BaseDocumentClassBuilder as CommonBaseDocumentClassBuilder;

class BaseDocumentClassBuilder extends CommonBaseDocumentClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Aggregate\\Base';
    }
}
