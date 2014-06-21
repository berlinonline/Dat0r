<?php

namespace Dat0r\CodeGen\Parser\TypeSchema;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\CodeGen\Schema\AttributeDefinition;
use Dat0r\CodeGen\Schema\AttributeDefinitionList;
use DOMXPath;
use DOMElement;

class AttributeDefinitionXpathParser extends XpathParser
{
    protected $short_names = array();

    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $attribute_set = new AttributeDefinitionList();

        foreach ($xpath->query('./attribute', $context) as $element) {
            $attribute_set->addItem($this->parseAttribute($xpath, $element));
        }

        return $attribute_set;
    }

    protected function parseAttribute(DOMXPath $xpath, DOMElement $element)
    {
        $description = '';
        $type = $element->getAttribute('type');
        $implementor = $this->resolveImplementor($type);
        $description_element = $xpath->query('./description', $element)->item(0);

        if ($description_element) {
            $description = $this->parseDescription($xpath, $description_element);
        }

        return new AttributeDefinition(
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
        $core_attribute_implementor = sprintf(
            "\\Dat0r\\Runtime\\Attribute\\Type\\%s",
            preg_replace_callback(
                '/(?:^|-)(.?)/',
                function ($matches) {
                    return strtoupper($matches[1]);
                },
                $type
            )
        );

        if (!class_exists($core_attribute_implementor)) {
            throw new RuntimeException(
                "Unable to resolve given type/short-name: '$type' to an existing implementor."
            );
        }

        return $core_attribute_implementor;
    }
}
