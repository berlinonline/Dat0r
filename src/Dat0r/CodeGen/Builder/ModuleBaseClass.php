<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class ModuleBaseClass extends ModuleClass
{
    const NS_MODULE = 'Dat0r\\Core\\Module';

    protected function getTemplate(Schema\ModuleSchema $module_schema)
    {
        return 'Module/BaseModule.twig';
    }

    protected function getParentImplementor(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $parent_class = $module_definition->getImplementor();

        if (!$parent_class)
        {
            $parent_class = sprintf('%s\\RootModule', self::NS_MODULE);
        }

        return $parent_class;
    }

    protected function getTemplateVars(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $module_name = $module_definition->getName();
        $namespace = $module_schema->getNamespace() . '\\' . $module_name;
        $base_package = $namespace . '\\Base';

        return array_merge(
            parent::getTemplateVars($module_schema),
            array(
                'namespace' => $base_package,
                'document_implementor' => sprintf('%s\\%sDocument', $namespace, $module_name)
            )
        );
    }
}
