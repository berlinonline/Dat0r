<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

use Dat0r\CodeGen\ClassBuilder\ClassBuilder;

class DocumentClassBuilder extends ClassBuilder
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
