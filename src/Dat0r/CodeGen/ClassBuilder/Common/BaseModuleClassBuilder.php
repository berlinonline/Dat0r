<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

use Dat0r\CodeGen\Schema\FieldDefinition;
use Dat0r\CodeGen\Schema\OptionDefinitionList;

class BaseModuleClassBuilder extends ModuleClassBuilder
{
    protected function getTemplate()
    {
        return 'Module/BaseModule.twig';
    }

    protected function getPackage()
    {
        return parent::getPackage() . '\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->module_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\RootModule', self::NS_MODULE);
        }

        return $parent_implementor;
    }

    protected function getTemplateVars()
    {
        $module_class_vars = array(
            'fields' => $this->prepareFieldsData(),
            'document_implementor' => $this->getDocumentImplementor(),
            'module_name' => $this->module_definition->getName(),
            'options' => $this->preRenderOptions($this->module_definition->getOptions(), 12)
        );

        return array_merge(parent::getTemplateVars(), $module_class_vars);
    }

    protected function getDocumentImplementor()
    {
        return var_export(
            sprintf('\\%1$s\\%2$s\\%2$sDocument', $this->getRootNamespace(), $this->module_definition->getName()),
            true
        );
    }

    protected function prepareFieldsData()
    {
        $fields_data = array();

        foreach ($this->module_definition->getFields() as $field_definition) {
            $field_implementor = $field_definition->getImplementor();

            if ($field_definition->getShortName() === 'aggregate') {
                $this->expandAggregateNamespaces($field_definition);
            }
            if ($field_definition->getShortName() === 'reference') {
                $this->expandReferenceNamespaces($field_definition);
            }

            $fieldname = $field_definition->getName();
            $fieldname_studlycaps = preg_replace('/(?:^|_)(.?)/e', "strtoupper('$1')", $fieldname);
            $field_getter = 'get' . $fieldname_studlycaps;
            $field_setter = 'set' . $fieldname_studlycaps;

            $fields_data[] = array(
                'implementor' => var_export($field_implementor, true),
                'class_name' => $field_implementor,
                'name' => $fieldname,
                'options' => $this->preRenderOptions($field_definition->getOptions(), 20)
            );
        }

        return $fields_data;
    }

    protected function expandAggregateNamespaces(FieldDefinition $field_definition)
    {
        $module_options = $field_definition->getOptions()->filterByName('modules');
        if ($module_options) {
            foreach ($module_options->getValue() as $module_option) {
                $module_option->setValue(
                    sprintf(
                        '\\%s\\%s\\Aggregate\\%sModule',
                        $this->getRootNamespace(),
                        $this->module_definition->getName(),
                        $module_option->getValue()
                    )
                );
            }
        }
    }

    protected function expandReferenceNamespaces(FieldDefinition $field_definition)
    {
        $reference_options = $field_definition->getOptions()->filterByName('references');
        if ($reference_options) {
            foreach ($reference_options->getValue() as $reference_option) {
                $reference_option->setValue(
                    sprintf(
                        '\\%s\\%s\\Reference\\%sModule',
                        $this->getRootNamespace(),
                        $this->module_definition->getName(),
                        $reference_option->getValue()
                    )
                );
            }
        }
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
