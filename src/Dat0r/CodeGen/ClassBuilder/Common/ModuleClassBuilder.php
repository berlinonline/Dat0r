<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

class ModuleClassBuilder extends ClassBuilder
{
    protected function getTemplate()
    {
        return 'Module/Module.twig';
    }

    protected function getImplementor()
    {
        return $this->module_definition->getName() . 'Module';
    }
}
