<?php

namespace Dat0r\CodeGen\Parser\ModuleSchema;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\CodeGen\Schema\FieldDefinition;
use Dat0r\CodeGen\Schema\FieldDefinitionList;
use DOMXPath;
use DOMElement;

class FieldDefinitionXpathParser extends XpathParser
{
    protected $short_names = array();

    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $field_set = FieldDefinitionList::create();

        foreach ($xpath->query('./field', $context) as $element) {
            $field_set->addItem($this->parseField($xpath, $element));
        }

        return $field_set;
    }

    protected function parseField(DOMXPath $xpath, DOMElement $element)
    {
        $description = '';
        $type = $element->getAttribute('type');
        $implementor = $this->resolveImplementor($type);

        if (($description_element = $xpath->query('./description', $element)->item(0))) {
            $description = $this->parseDescription(
                $xpath,
                $xpath->query('./description', $element)->item(0)
            );
        }

        return FieldDefinition::create(
            array(
                'name' => $element->getAttribute('name'),
                'short_name' => ($implementor == $type) ? null : $type,
                'implementor' => $implementor,
                'description' => $description,
                'options' => $this->parseOptions($xpath, $element)
            )
        );
    }

    protected function resolveImplementor($type)
    {
        if (isset($this->short_names[$type])) {
            return $this->short_names[$type];
        }
        // @todo allow to register a custom shortname map to extend the core definitions.
        $core_field_implementor = sprintf(
            "\\Dat0r\\Runtime\\Field\\Type\\%sField",
            preg_replace(
                '/(?:^|-)(.?)/e',
                "strtoupper('$1')",
                $type
            )
        );

        if (!class_exists($core_field_implementor)) {
            throw new RuntimeException(
                "Unable to resolve given type/short-name: '$type' to an existing implementor."
            );
        }

        return $core_field_implementor;
    }
}
