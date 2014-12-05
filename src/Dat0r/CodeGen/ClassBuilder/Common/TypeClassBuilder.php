<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

class TypeClassBuilder extends ClassBuilder
{
    protected function getTemplate()
    {
        return 'Type/Type.twig';
    }

    protected function getImplementor()
    {
        $class_suffix = $this->config->getTypeSuffix('Type');

        return $this->type_definition->getName() . ucfirst($class_suffix);
    }
}
