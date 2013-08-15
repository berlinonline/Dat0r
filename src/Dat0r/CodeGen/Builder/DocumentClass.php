<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

class DocumentClass extends ClassBuilder
{
    protected function getImplementor()
    {
        return $this->module_definition->getName() . 'Document';
    }

    protected function getTemplate()
    {
        return 'Document/Document.twig';
    }
}
