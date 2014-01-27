<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\ModuleClassBuilder as CommonModuleClassBuilder;

class ModuleClassBuilder extends CommonModuleClassBuilder
{
    protected function getPackage()
    {
        return $this->module_schema->getPackage() . '\\Reference';
    }
}
