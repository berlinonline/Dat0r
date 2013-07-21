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

    protected function buildPackage()
    {
        return parent::buildPackage() . '\\Base';
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
        return array_merge(
            parent::getTemplateVars(),
            array(
                'document_implementor' => sprintf(
                    '%s\\%s\\%sDocument',
                    $this->buildNamespace(),
                    parent::buildPackage(),
                    $this->module_definition->getName()
                )
            )
        );
    }
}
