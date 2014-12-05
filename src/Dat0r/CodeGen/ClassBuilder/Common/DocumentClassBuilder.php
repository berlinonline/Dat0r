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
        $class_suffix = $this->config->getEntitySuffix('Document');

        return $this->type_definition->getName() . ucfirst($class_suffix);
    }
}
