<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

use Dat0r\CodeGen\ClassBuilder\Common\BaseTypeClassBuilder as CommonBaseTypeClassBuilder;

class BaseTypeClassBuilder extends CommonBaseTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Aggregate\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->type_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\Aggregate', self::NS_MODULE);
        }

        return $parent_implementor;
    }

    protected function getDocumentImplementor()
    {
        return var_export(
            sprintf(
                '\\%s\\%s\\Aggregate\\%sDocument',
                $this->getRootNamespace(),
                $this->type_schema->getPackage(),
                $this->type_definition->getName()
            ),
            true
        );
    }
}
