<?php

namespace Dat0r\CodeGen\ClassBuilder;

use Dat0r\Common\Object;
use Twig_Loader_Filesystem;

abstract class ClassBuilder extends Object implements IClassBuilder
{
    const NS_FIELDS = '\\Dat0r\\Runtime\\Field\\Type';

    const NS_MODULE = '\\Dat0r\\Runtime\\Module';

    const NS_DOCUMENT = '\\Dat0r\\Runtime\\Document';

    protected $twig;

    protected $module_schema;

    protected $module_definition;

    abstract protected function getImplementor();

    abstract protected function getParentImplementor();

    abstract protected function getTemplate();

    abstract protected function getRootNamespace();

    abstract protected function getPackage();

    public function __construct()
    {
        $this->twig = new \Twig_Environment(
            new Twig_Loader_Filesystem(
                __DIR__ . DIRECTORY_SEPARATOR . 'templates'
            )
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
            'description' => $this->module_definition->getDescription(),
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
}
