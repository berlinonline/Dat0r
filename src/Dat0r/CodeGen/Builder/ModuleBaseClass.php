<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema\AggregateDefinition;
use Dat0r\CodeGen\Schema\FieldDefinition;

class ModuleBaseClass extends ModuleClass
{
    const NS_MODULE = '\\Dat0r\\Core\\Module';

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

        if ($parent_implementor === null) {
            $parent_implementor = sprintf(
                '%s\\%s',
                ModuleBaseClass::NS_MODULE,
                ($this->module_definition instanceof AggregateDefinition)
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
                'document_implementor' => var_export(
                    sprintf(
                        '\\%s\\%s\\%sDocument',
                        $this->buildNamespace(),
                        parent::buildPackage(),
                        $this->module_definition->getName()
                    ),
                    true
                )
            )
        );
    }

    protected function expandAggregateNamespaces(FieldDefinition $field_definition)
    {
        foreach ($field_definition->getOptions() as $option) {
            if ($option->getName() === 'modules') {
                foreach ($option->getValue() as $module_option) {
                    $module_option->setValue(
                        sprintf(
                            '\\%s\\%s\\%s',
                            $this->buildNamespace(),
                            parent::buildPackage(),
                            $module_option->getValue() . 'Module'
                        )
                    );
                }
            }
        }
    }
}
