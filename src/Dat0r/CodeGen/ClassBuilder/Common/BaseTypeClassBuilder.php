<?php

namespace Dat0r\CodeGen\ClassBuilder\Common;

use Dat0r\CodeGen\Schema\AttributeDefinition;
use Dat0r\CodeGen\Schema\OptionDefinitionList;

class BaseTypeClassBuilder extends TypeClassBuilder
{
    protected function getTemplate()
    {
        return 'Type/BaseType.twig';
    }

    protected function getPackage()
    {
        return parent::getPackage() . '\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->type_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\DocumentType', self::NS_MODULE);
        }

        return $parent_implementor;
    }

    protected function getTemplateVars()
    {
        $type_class_vars = array(
            'attributes' => $this->prepareAttributeData(),
            'document_implementor' => $this->getDocumentImplementor(),
            'type_name' => $this->type_definition->getName(),
            'options' => $this->preRenderOptions($this->type_definition->getOptions(), 12)
        );

        return array_merge(parent::getTemplateVars(), $type_class_vars);
    }

    protected function getDocumentImplementor()
    {
        return var_export(
            sprintf('\\%1$s\\%2$s\\%2$sDocument', $this->getRootNamespace(), $this->type_definition->getName()),
            true
        );
    }

    protected function prepareAttributeData()
    {
        $attributes_data = array();

        foreach ($this->type_definition->getAttributes() as $attribute_definition) {
            $attribute_implementor = $attribute_definition->getImplementor();

            if ($attribute_definition->getShortName() === 'aggregate') {
                $this->expandAggregateNamespaces($attribute_definition);
            }
            if ($attribute_definition->getShortName() === 'reference') {
                $this->expandReferenceNamespaces($attribute_definition);
            }

            $attributename = $attribute_definition->getName();
            $attributename_studlycaps = preg_replace_callback(
                '/(?:^|_)(.?)/',
                function ($matches) {
                    return strtoupper($matches[1]);
                },
                $attributename
            );

            $attribute_getter = 'get' . $attributename_studlycaps;
            $attribute_setter = 'set' . $attributename_studlycaps;

            $attributes_data[] = array(
                'implementor' => var_export($attribute_implementor, true),
                'class_name' => $attribute_implementor,
                'name' => $attributename,
                'options' => $this->preRenderOptions($attribute_definition->getOptions(), 20)
            );
        }

        return $attributes_data;
    }

    protected function expandAggregateNamespaces(AttributeDefinition $attribute_definition)
    {
        $type_options = $attribute_definition->getOptions()->filterByName('types');
        if ($type_options) {
            foreach ($type_options->getValue() as $type_option) {
                $type_option->setValue(
                    sprintf(
                        '\\%s\\%s\\Aggregate\\%sType',
                        $this->getRootNamespace(),
                        $this->type_schema->getPackage(),
                        $type_option->getValue()
                    )
                );
            }
        }
    }

    protected function expandReferenceNamespaces(AttributeDefinition $attribute_definition)
    {
        $reference_options = $attribute_definition->getOptions()->filterByName('references');
        if ($reference_options) {
            foreach ($reference_options->getValue() as $reference_option) {
                $reference_option->setValue(
                    sprintf(
                        '\\%s\\%s\\Reference\\%sType',
                        $this->getRootNamespace(),
                        $this->type_schema->getPackage(),
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
