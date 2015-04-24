<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

use Dat0r\CodeGen\ClassBuilder\ClassBuilder as BaseClassBuilder;

abstract class ClassBuilder extends BaseClassBuilder
{
    const NS_FIELDS = '\\Dat0r\\Runtime\\Attribute\\Type';

    const NS_MODULE = '\\Dat0r\\Runtime';

    const NS_ENTITY = '\\Dat0r\\Runtime\\Entity';

    protected $type_schema;

    protected $type_definition;

    protected function getDescription()
    {
        return $this->type_definition->getDescription();
    }

    protected function getRootNamespace()
    {
        return $this->type_schema->getNamespace();
    }

    protected function getPackage()
    {
        return $this->type_schema->getPackage();
    }

    protected function getImplementor()
    {
        $class_suffix = $this->config->getTypeSuffix('Type');

        return $this->type_definition->getName() . ucfirst($class_suffix);
    }

    protected function getParentImplementor()
    {
        return sprintf('\\%s\\Base\\%s', $this->getNamespace(), $this->getImplementor());
    }

    protected function getTemplateVars()
    {
        $basic_class_vars = array(
            'type_name' => $this->type_definition->getName()
        );

        return array_merge(parent::getTemplateVars(), $basic_class_vars);
    }
}
