<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class DocumentClass extends ClassBuilder
{
    protected function getImplementor(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $module_name = $module_definition->getName();

        return sprintf('%sDocument', $module_name);
    }

    protected function getTemplate(Schema\ModuleSchema $module_schema)
    {
        return 'Document/Document.twig';
    }
}
