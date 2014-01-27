<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\BaseModuleClassBuilder as CommonBaseModuleClassBuilder;

class BaseModuleClassBuilder extends CommonBaseModuleClassBuilder
{
    protected function getPackage()
    {
        return $this->module_schema->getPackage() . '\\Reference\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->module_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\ReferenceModule', self::NS_MODULE);
        }

        return $parent_implementor;
    }
}
