<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\ClassBuilder;

class ModuleClassBuilder extends ClassBuilder
{
    protected function getImplementor()
    {
        return $this->module_definition->getName() . 'Module';
    }

    protected function getTemplate()
    {
        return 'Module/Module.twig';
    }
}
