<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class DocumentClass extends ClassBuilder
{
    protected function getImplementor()
    {
        $module_name = $this->module_definition->getName();

        return sprintf('%sDocument', $module_name);
    }

    protected function getTemplate()
    {
        return 'Document/Document.twig';
    }
}
