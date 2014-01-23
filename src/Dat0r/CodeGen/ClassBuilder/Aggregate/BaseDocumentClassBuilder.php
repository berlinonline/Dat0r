<?php

namespace Dat0r\CodeGen\ClassBuilder\Aggregate;

class BaseDocumentClassBuilder extends DocumentClassBuilder
{
    protected function getTemplate()
    {
        return 'Document/BaseDocument.twig';
    }

    protected function buildPackage()
    {
        return parent::buildPackage() . '\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_class = $this->module_definition->getDocumentImplementor();

        if (!$parent_class) {
            $parent_class = sprintf('\\%s\\Document', self::NS_DOCUMENT);
        }

        return $parent_class;
    }
}
