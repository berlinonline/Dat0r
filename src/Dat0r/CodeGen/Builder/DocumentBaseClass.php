<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class DocumentBaseClass extends DocumentClass
{
    const NS_DOCUMENT = 'Dat0r\\Core\\Document';

    protected function getTemplate(Schema\ModuleSchema $module_schema)
    {
        return 'Document/BaseDocument.twig';
    }

    protected function getParentImplementor(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $parent_class = $module_definition->getDocumentImplementor();

        if (!$parent_class)
        {
            $parent_class = sprintf('%s\\Document', self::NS_DOCUMENT);
        }

        return $parent_class;
    }

    protected function getTemplateVars(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $module_name = $module_definition->getName();
        $namespace = $module_schema->getNamespace() . '\\' . $module_name . '\\Base';

        return array_merge(
            parent::getTemplateVars($module_schema),
            array('namespace' => $namespace)
        );
    }
}
