<?php

namespace Dat0r\CodeGen\Builder;

use Dat0r\CodeGen\Schema\ModuleSchema;
use Dat0r\CodeGen\Schema\ModuleDefinition;
use Dat0r\CodeGen\Schema\FieldDefinition;
use Dat0r\CodeGen\Schema\FieldDefinitionList;
use Dat0r\CodeGen\Schema\OptionDefinition;
use Dat0r\CodeGen\Schema\OptionDefinitionList;

abstract class ClassBuilder implements IClassBuilder
{
    const NS_FIELDS = '\\Dat0r\\Runtime\\Field';

    protected $twig;

    protected $module_schema;

    protected $module_definition;

    abstract protected function getImplementor();

    abstract protected function getTemplate();

    public static function create(ModuleSchema $module_schema, ModuleDefinition $module_definition = null)
    {
        return new static($module_schema, $module_definition);
    }

    public function __construct(ModuleSchema $module_schema, ModuleDefinition $module_definition = null)
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
        $implementor = implode('\\', array_filter(explode('\\', $this->getImplementor())));

        $parent_class = $this->getParentImplementor();
        $parent_class_parts = array_filter(explode('\\', $parent_class));
        $parent_implementor = array_pop($parent_class_parts);
        $parent_package = end($parent_class_parts);
        $parent_namespace = implode('\\', $parent_class_parts);
        $fields_data = $this->buildFieldDefinitionData($this->module_definition->getFields());

        $namespaces = $fields_data['namespaces'];
        array_unshift($namespaces, $parent_namespace);
        asort($namespaces);

        return array(
            'datetime' => date('Y-m-d H:i:s'),
            'module_name' => $module_name,
            'description' => $this->module_definition->getDescription(),
            'namespace' => sprintf('%s\%s', $this->buildNamespace(), $this->buildPackage()),
            'parent_namespace' => $parent_namespace,
            'parent_package' => $parent_package,
            'parent_implementor' => $parent_implementor,
            'implementor' => $implementor,
            'fields' => $fields_data['instances'],
            'namespaces_to_use' => $namespaces,
            'options' => $this->preRenderOptions($this->module_definition->getOptions(), 12)
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
            '\\%s\\%s\\Base\\%s',
            $this->buildNamespace(),
            $this->buildPackage(),
            $this->getImplementor()
        );
    }

    protected function buildFieldDefinitionData(FieldDefinitionList $fields)
    {
        $fields_data = array();
        $namespaces = array();

        foreach ($fields as $field_definition) {
            $field_name = $field_definition->getName();
            $field_name_studlycaps = preg_replace('/(?:^|_)(.?)/e', "strtoupper('$1')", $field_name);

            $field_implementor = $field_definition->getImplementor();
            $class_name = $field_implementor;
            if (strpos($field_implementor, '\\Dat0r\\Runtime\\Field\\') === 0) {
                $implementor_parts = explode('\\', $field_implementor);
                $class_name = array_pop($implementor_parts);
                $use = implode('\\', array_filter(explode('\\', $field_implementor)));
                if (!in_array($use, $namespaces)) {
                    $namespaces[] = $use;
                }
            }

            if ($field_definition->getShortName() === 'aggregate') {
                $this->expandAggregateNamespaces($field_definition);
            }

            $fields_data[] = array(
                'implementor' => var_export($field_implementor, true),
                'class_name' => $class_name,
                'name' => $field_name,
                'setter' => 'set' . $field_name_studlycaps,
                'getter' => 'get' . $field_name_studlycaps,
                'options' => $this->preRenderOptions($field_definition->getOptions(), 20)
            );
        }

        return array('instances' => $fields_data, 'namespaces' => $namespaces);
    }

    protected function expandAggregateNamespaces(FieldDefinition $field_definition)
    {
    }

    protected function preRenderOptions(OptionDefinitionList $options, $initial_indent = 0, $indent_size = 4)
    {
        if ($options->getSize() === 0) {
            return 'array()';
        }

        $options_code = array('array(');
        $indent_spaces = str_repeat(" ", $initial_indent + $indent_size);
        $next_level_indent = $initial_indent + $indent_size;
        foreach ($options as $option) {
            $option_name = $option->getName();
            $option_value = $option->getValue();
            if ($option_name && $option_value instanceof OptionDefinitionList) {
                $options_code[] = sprintf(
                    "%s'%s' => %s,",
                    $indent_spaces,
                    $option_name,
                    $this->preRenderOptions($option_value, $next_level_indent)
                );
            } elseif ($option_value instanceof OptionDefinitionList) {
                $options_code[] = sprintf(
                    "%s%s,",
                    $indent_spaces,
                    $this->preRenderOptions($option_value, $next_level_indent)
                );
            } elseif ($option_name) {
                $options_code[] = sprintf(
                    "%s'%s' => %s,",
                    $indent_spaces,
                    $option_name,
                    var_export($option_value, true)
                );
            } else {
                $options_code[] = sprintf("%s%s,", $indent_spaces, var_export($option_value, true));
            }
        }
        $options_code[] = sprintf('%s)', str_repeat(" ", $initial_indent));

        return implode(PHP_EOL, $options_code);
    }
}
