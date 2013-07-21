<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class ModuleClass extends ClassBuilder
{
    protected function getImplementor()
    {
        $module_name = $this->module_definition->getName();

        return sprintf('%sModule', $module_name);
    }

    protected function getTemplate()
    {
        return 'Module/Module.twig';
    }
}
