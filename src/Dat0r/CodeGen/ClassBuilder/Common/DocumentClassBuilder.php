<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

class DocumentClassBuilder extends ClassBuilder
{
    protected function getTemplate()
    {
        return 'Document/Document.twig';
    }

    protected function getImplementor()
    {
        return $this->type_definition->getName() . 'Document';
    }
}
