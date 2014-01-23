<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\Schema\FieldDefinition;

class BaseModuleClassBuilder extends ModuleClassBuilder
{
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
            $parent_implementor = sprintf('%s\\ReferenceModule', self::NS_MODULE);
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

    protected function expandReferenceNamespaces(FieldDefinition $field_definition)
    {
        foreach ($field_definition->getOptions() as $option) {
            if ($option->getName() === 'references') {
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
