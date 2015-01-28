<?php

namespace Dat0r\CodeGen\ClassBuilder\Embed;

use Dat0r\CodeGen\ClassBuilder\Common\BaseEntityTypeClassBuilder;

class BaseEmbedTypeClassBuilder extends BaseEntityTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Embed\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->type_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\Embed', self::NS_MODULE);
        }

        return $parent_implementor;
    }

    protected function getEntityImplementor()
    {
        return var_export(
            sprintf(
                '\\%s\\%s\\Embed\\%sEntity',
                $this->getRootNamespace(),
                $this->type_schema->getPackage(),
                $this->type_definition->getName()
            ),
            true
        );
    }
}
