<?php

namespace Dat0r\CodeGen\ClassBuilder\Reference;

use Dat0r\CodeGen\ClassBuilder\Common\BaseTypeClassBuilder as CommonBaseTypeClassBuilder;

class BaseTypeClassBuilder extends CommonBaseTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->type_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\Reference', self::NS_MODULE);
        }

        return $parent_implementor;
    }

    protected function getEntityImplementor()
    {
        return var_export(
            sprintf(
                '\\%s\\%s\\Reference\\%sEntity',
                $this->getRootNamespace(),
                $this->type_schema->getPackage(),
                $this->type_definition->getName()
            ),
            true
        );
    }
}
