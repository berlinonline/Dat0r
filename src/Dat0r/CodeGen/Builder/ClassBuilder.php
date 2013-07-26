<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

abstract class ClassBuilder implements IClassBuilder
{
    const NS_FIELDS = 'Dat0r\Core\\Field';

    protected $twig;

    protected $module_schema;

    protected $module_definition;

    abstract protected function getImplementor();

    abstract protected function getTemplate();

    public static function create(
        Schema\ModuleSchema $module_schema,
        Schema\ModuleDefinition $module_definition = null
    ) {
        return new static($module_schema, $module_definition);
    }

    public function __construct(
        Schema\ModuleSchema $module_schema,
        Schema\ModuleDefinition $module_definition = null
    ) {
        $this->twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(
                __DIR__ . DIRECTORY_SEPARATOR . 'templates'
            )
        );

        $this->module_schema = $module_schema;
        $this->module_definition = $module_definition
            ? $module_definition
            : $module_schema->getModuleDefinition();
    }

    public function build()
    {
        $this->module_schema->getModuleDefinition();
        $implementor = $this->getImplementor();

        return ClassContainer::create(
            array(
                'file_name' => $implementor . '.php',
                'class_name' => $implementor,
                'namespace' => $this->buildNamespace(),
                'package' => $this->buildPackage(),
                'source_code' => $this->twig->render(
                    $this->getTemplate(),
                    $this->getTemplateVars()
                )
            )
        );
    }

    protected function getTemplateVars()
    {
        $module_name = $this->module_definition->getName();
        $implementor = $this->getImplementor();

        $parent_class = $this->getParentImplementor();
        $parent_class_parts = array_filter(explode('\\', $parent_class));
        $parent_implementor = array_pop($parent_class_parts);
        $parent_package = end($parent_class_parts);
        $parent_namespace = implode('\\', $parent_class_parts);

        return array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $module_name,
            'description' => $this->module_definition->getDescription(),
            'namespace' => sprintf('%s\\%s', $this->buildNamespace(), $this->buildPackage()),
            'parent_namespace' => $parent_namespace,
            'parent_package' => $parent_package,
            'parent_implementor' => $parent_implementor,
            'implementor' => $implementor,
            'fields' => $this->buildFieldDefinitionData($this->module_definition->getFields()),
            'options' => $this->preRenderOptions($this->module_definition->getOptions())
        );
    }

    protected function buildNamespace()
    {
        return $this->module_schema->getNamespace();
    }

    protected function buildPackage()
    {
        return $this->module_schema->getPackage();
    }

    protected function getParentImplementor()
    {
        $module_name = $this->module_definition->getName();

        return sprintf(
            '%s\\%s\\Base\\%s',
            $this->buildNamespace(),
            $this->buildPackage(),
            $this->getImplementor()
        );
    }

    protected function buildFieldDefinitionData(Schema\FieldDefinitionSet $fields)
    {
        $fields_data = array();

        foreach ($fields as $field_definition) {
            $camel_caps_type = preg_replace(
                '/(?:^|-)(.?)/e',
                "strtoupper('$1')",
                $field_definition->getType()
            );

            $field_implementor = sprintf('%s\\%sField', self::NS_FIELDS, $camel_caps_type);
            $field_name = preg_replace(
                '/(?:^|_)(.?)/e',
                "strtoupper('$1')",
                $field_definition->getName()
            );

            if ($field_definition->getType() === 'aggregate') {
                foreach ($field_definition->getOptions() as $option) {
                    if ($option->getName() === 'modules') {
                        foreach ($option->getValue() as $module_option) {
                            $module_option->setValue(
                                sprintf(
                                    '%s\\%s\\%s',
                                    $this->buildNamespace(),
                                    $this->buildPackage(),
                                    $module_option->getValue()
                                )
                            );
                        }
                    }
                }
            }

            $fields_data[] = array(
                'implementor' => $field_implementor,
                'name' => lcfirst($field_name),
                'setter' => 'set' . $field_name,
                'getter' => 'get' . $field_name,
                'options' => $this->preRenderOptions($field_definition->getOptions())
            );
        }

        return $fields_data;
    }

    protected function preRenderOptions(Schema\OptionDefinitionList $options)
    {
        $pre_rendered_options = '';

        if ($options->getSize() > 0) {
            $pre_rendered_options = preg_replace(
                array('/array\s*\(\s*/is', '/,\s+/is', '/\d+\s+=>\s+/is', '/\s+=>\s+/is'),
                array("array(", ', ', '', ' => '),
                preg_replace('/\n/is', '', var_export($options->toArray(), true))
            );
        }

        return $pre_rendered_options;
    }
}
