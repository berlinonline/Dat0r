<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

use Dat0r\CodeGen\ClassBuilder\ClassBuilder;

class ModuleClassBuilder extends ClassBuilder
{
    protected function getTemplate()
    {
        return 'Module/Module.twig';
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
        return $this->module_definition->getName() . 'Module';
    }

    protected function getParentImplementor()
    {
        return sprintf('\\%s\\Base\\%s', $this->getNamespace(), $this->getImplementor());
    }
}
