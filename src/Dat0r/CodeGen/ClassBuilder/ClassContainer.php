<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;

class ClassContainer extends Object implements ClassContainerInterface
{
    protected $file_name;

    protected $namespace;

    protected $package;

    protected $class_name;

    protected $source_code;

    public function getFileName()
    {
        return $this->file_name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    protected function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        $namespace_parts = explode('\\', $this->namespace);
        $this->package = end($namespace_parts);
    }

    public function getPackage()
    {
        return $this->package;
    }

    public function getClassName()
    {
        return $this->class_name;
    }

    public function getSourceCode()
    {
        return $this->source_code;
    }
}
