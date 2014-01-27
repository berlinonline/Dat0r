<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\BaseDocumentClassBuilder as CommonBaseDocumentClassBuilder;

class BaseDocumentClassBuilder extends CommonBaseDocumentClassBuilder
{
    protected function getPackage()
    {
        return $this->module_schema->getPackage() . '\\Reference\\Base';
    }
}
