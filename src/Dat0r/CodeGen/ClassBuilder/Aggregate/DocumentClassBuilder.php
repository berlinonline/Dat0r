<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\Common\DocumentClassBuilder as CommonDocumentClassBuilder;

class DocumentClassBuilder extends CommonDocumentClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Aggregate';
    }
}
