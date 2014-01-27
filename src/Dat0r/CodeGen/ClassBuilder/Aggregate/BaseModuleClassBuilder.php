<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\Common\BaseModuleClassBuilder as CommonBaseModuleClassBuilder;

class BaseModuleClassBuilder extends CommonBaseModuleClassBuilder
{
    protected function getPackage()
    {
        return $this->module_schema->getPackage() . '\\Aggregate\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->module_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\AggregateModule', self::NS_MODULE);
        }

        return $parent_implementor;
    }
}
