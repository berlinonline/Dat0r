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
    )
    {
        return new static($module_schema, $module_definition);
    }

    public function build()
    {
        $this->module_schema->getModuleDefinition();
        $implementor = $this->getImplementor();

        return ClassContainer::create(array(
            'file_name' => $implementor . '.php',
            'class_name' => $implementor,
            'namespace' => sprintf(
                '%s\\%s\\Base',
                $this->module_schema->getNamespace(),
                $this->module_definition->getName()
            ),
            'source_code' => $this->twig->render(
                $this->getTemplate(),
                $this->getTemplateVars()
            )
        ));
    }

    protected function __construct(
        Schema\ModuleSchema $module_schema,
        Schema\ModuleDefinition $module_definition = null
    )
    {
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

    protected function getTemplateVars()
    {
        $module_name = $this->module_definition->getName();
        $implementor = $this->getImplementor();
        $namespace = $this->module_schema->getNamespace() . '\\' . $module_name;

        $parent_class = $this->getParentImplementor();
        $parent_class_parts = array_filter(explode('\\', $parent_class));
        $parent_implementor = array_pop($parent_class_parts);
        $parent_package = end($parent_class_parts);
        $parent_namespace = implode('\\', $parent_class_parts);

        return array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $module_name,
            'description' => $this->module_definition->getDescription(),
            'namespace' => $namespace,
            'parent_namespace' => $parent_namespace,
            'parent_package' => $parent_package,
            'parent_implementor' => $parent_implementor,
            'implementor' => $implementor,
            'fields' => $this->buildFieldDefinitionData($this->module_definition->getFields()),
            'options' => $this->formatOptions($this->module_definition->getOptions())
        );
    }

    protected function getParentImplementor()
    {
        $module_name = $this->module_definition->getName();
        $implementor = $this->getImplementor();
        $namespace = $this->module_schema->getNamespace() . '\\' . $module_name;
        $base_package = $namespace . '\\Base';

        return $base_package . '\\' . $implementor;
    }

    protected function buildFieldDefinitionData(Schema\FieldDefinitionSet $fields)
    {
        $fields_data = array();

        foreach ($fields as $field_definition)
        {
            $camelcased_type = preg_replace_callback('/_(.)/', function($matches)
            {
                return strtoupper($matches[1]);
            }, $field_definition->getType());

            $field_implementor = sprintf(
                '%s\\%sField',
                self::NS_FIELDS,
                ucfirst($camelcased_type)
            );

            $fields_data[] = array(
                'implementor' => $field_implementor,
                'name' => $field_definition->getName(),
                'options' => $this->formatOptions($field_definition->getOptions())
            );
        }

        return $fields_data;
    }

    protected function formatOptions(Schema\OptionDefinitionList $options)
    {
        $formatted_options = array();

        foreach ($options as $option)
        {
            $formatted_value = preg_replace(
                array('/array\s*\(\s*/is', '/,\s+/is', '/\d+\s+=>\s+/is', '/\s+=>\s+/is'),
                array("array(", ', ', '', ' => '),
                preg_replace('/\n/is', '', var_export($option->getValue(), TRUE))
            );

            $formatted_options[$option->getName()] = array(
                'name' => $option->getName(),
                'value' => $formatted_value
            );
        }

        return $formatted_options;
    }
}
