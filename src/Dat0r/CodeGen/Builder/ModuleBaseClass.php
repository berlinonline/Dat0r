<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class ModuleBaseClass extends ModuleClass
{
    const NS_MODULE = 'Dat0r\\Core\\Module';

    protected function getTemplate()
    {
        return 'Module/BaseModule.twig';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->module_definition->getImplementor();

        if (!$parent_implementor)
        {
            $parent_implementor = sprintf(
                '%s\\%s',
                ModuleBaseClass::NS_MODULE,
                ($this->module_definition instanceof Schema\AggregateDefinition)
                ? 'AggregateModule'
                : 'RootModule'
            );
        }

        return $parent_implementor;
    }

    protected function getTemplateVars()
    {
        $module_name = $this->module_definition->getName();
        $namespace = $this->module_schema->getNamespace() . '\\' . $module_name;
        $base_package = $namespace . '\\Base';

        return array_merge(
            parent::getTemplateVars(),
            array(
                'namespace' => $base_package,
                'document_implementor' => sprintf('%s\\%sDocument', $namespace, $module_name)
            )
        );
    }
}
