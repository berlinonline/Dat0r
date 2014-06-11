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
        return $this->type_definition->getName() . 'Type';
    }
}
