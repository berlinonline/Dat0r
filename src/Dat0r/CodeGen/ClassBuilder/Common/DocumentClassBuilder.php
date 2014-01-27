<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

use Dat0r\CodeGen\ClassBuilder\ClassBuilder;

class DocumentClassBuilder extends ClassBuilder
{
    protected function getTemplate()
    {
        return 'Document/Document.twig';
    }

    protected function getRootNamespace()
    {
        return $this->module_schema->getNamespace();
    }

    protected function getPackage()
    {
        return $this->module_schema->getPackage();
    }

    protected function getImplementor()
    {
        return $this->module_definition->getName() . 'Document';
    }

    protected function getParentImplementor()
    {
        return sprintf('\\%s\\Base\\%s', $this->getNamespace(), $this->getImplementor());
    }
}
