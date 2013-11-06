<?php

namespace Dat0r\CodeGen\Builder;

class ModuleClass extends ClassBuilder
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
