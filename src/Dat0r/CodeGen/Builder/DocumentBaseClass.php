<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class DocumentBaseClass extends DocumentClass
{
    const NS_DOCUMENT = 'Dat0r\\Core\\Document';

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
            $parent_class = sprintf('%s\\Document', self::NS_DOCUMENT);
        }

        return $parent_class;
    }
}
