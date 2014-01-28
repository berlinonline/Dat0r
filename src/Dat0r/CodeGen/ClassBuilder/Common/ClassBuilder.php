<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

use Dat0r\CodeGen\ClassBuilder\ClassBuilder as BaseClassBuilder;

abstract class ClassBuilder extends BaseClassBuilder
{
    const NS_FIELDS = '\\Dat0r\\Runtime\\Field\\Type';

    const NS_MODULE = '\\Dat0r\\Runtime\\Module';

    const NS_DOCUMENT = '\\Dat0r\\Runtime\\Document';

    protected $module_schema;

    protected $module_definition;

    protected function getDescription()
    {
        return $this->module_definition->getDescription();
    }

    protected function getRootNamespace()
    {
        return $this->module_schema->getNamespace();
    }

    protected function getPackage()
    {
        return $this->module_schema->getPackage();
    }

    protected function getParentImplementor()
    {
        return sprintf('\\%s\\Base\\%s', $this->getNamespace(), $this->getImplementor());
    }

    protected function getTemplateVars()
    {
        $basic_class_vars = array(
            'module_name' => $this->module_definition->getName()
        );

        return array_merge(parent::getTemplateVars(), $basic_class_vars);
    }
}
