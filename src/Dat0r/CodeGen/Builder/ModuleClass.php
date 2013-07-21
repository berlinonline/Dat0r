<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class ModuleClass extends ClassBuilder
{
    protected function getImplementor(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $module_name = $module_definition->getName();

        return sprintf('%sModule', $module_name);
    }

    protected function getTemplate(Schema\ModuleSchema $module_schema)
    {
        return 'Module/Module.twig';
    }
}
