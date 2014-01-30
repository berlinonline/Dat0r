<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;
use Twig_Environment;
use Twig_Loader_Filesystem;

abstract class ClassBuilder extends Object implements IClassBuilder
{
    protected $twig;

    abstract protected function getImplementor();

    abstract protected function getParentImplementor();

    abstract protected function getTemplate();

    abstract protected function getRootNamespace();

    abstract protected function getPackage();

    abstract protected function getDescription();

    public function __construct()
    {
        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem($this->getTemplateBaseDirectory())
        );
    }

    public function build()
    {
        $implementor = $this->getImplementor();
        return ClassContainer::create(
            array(
                'file_name' => $implementor . '.php',
                'class_name' => $implementor,
                'namespace' => $this->getRootNamespace(),
                'package' => $this->getPackage(),
                'source_code' => $this->twig->render(
                    $this->getTemplate(),
                    $this->getTemplateVars()
                )
            )
        );
    }

    protected function getTemplateVars()
    {
        $parent_class = $this->getParentImplementor();
        $parent_class_parts = array_filter(explode('\\', $parent_class));

        $template_vars = array(
            'description' => $this->getDescription(),
            'namespace' => $this->getNamespace(),
            'class_name' => $this->getImplementor(),
            'parent_class_name' => array_pop($parent_class_parts),
            'parent_implementor' => trim($this->getParentImplementor(), '\\')
        );

        return $template_vars;
    }

    protected function getNamespace()
    {
        return $this->getRootNamespace() . '\\' . $this->getPackage();
    }

    protected function getTemplateBaseDirectory()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'templates';
    }
}