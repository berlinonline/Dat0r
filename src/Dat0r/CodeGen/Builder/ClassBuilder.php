<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema;

abstract class ClassBuilder implements IClassBuilder
{
    const NS_FIELDS = 'Dat0r\Core\\Field';

    protected $twig;

    abstract protected function getImplementor(Schema\ModuleSchema $module_schema);

    abstract protected function getTemplate(Schema\ModuleSchema $module_schema);

    public static function create()
    {
        return new static();
    }

    public function build(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $implementor = $this->getImplementor($module_schema);

        return ClassContainer::create(array(
            'file_name' => $implementor . '.php',
            'class_name' => $implementor,
            'namespace' => sprintf(
                '%s\\%s\\Base',
                $module_schema->getNamespace(),
                $module_definition->getName()
            ),
            'source_code' => $this->twig->render(
                $this->getTemplate($module_schema),
                $this->getTemplateVars($module_schema)
            )
        ));
    }

    protected function __construct()
    {
        $this->twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(
                __DIR__ . DIRECTORY_SEPARATOR . 'templates'
            )
        );
    }

    protected function getTemplateVars(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $module_name = $module_definition->getName();
        $implementor = $this->getImplementor($module_schema);
        $namespace = $module_schema->getNamespace() . '\\' . $module_name;

        $parent_class = $this->getParentImplementor($module_schema);
        $parent_class_parts = array_filter(explode('\\', $parent_class));
        $parent_implementor = array_pop($parent_class_parts);
        $parent_package = end($parent_class_parts);
        $parent_namespace = implode('\\', $parent_class_parts);

        return array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $module_name,
            'description' => $module_definition->getDescription(),
            'namespace' => $namespace,
            'parent_namespace' => $parent_namespace,
            'parent_package' => $parent_package,
            'parent_implementor' => $parent_implementor,
            'implementor' => $implementor,
            'fields' => $this->buildFieldDefinitionData($module_definition->getFields()),
            'options' => $this->formatOptions($module_definition->getOptions())
        );
    }

    protected function getParentImplementor(Schema\ModuleSchema $module_schema)
    {
        $module_definition = $module_schema->getModuleDefinition();
        $module_name = $module_definition->getName();
        $implementor = $this->getImplementor($module_schema);
        $namespace = $module_schema->getNamespace() . '\\' . $module_name;
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
