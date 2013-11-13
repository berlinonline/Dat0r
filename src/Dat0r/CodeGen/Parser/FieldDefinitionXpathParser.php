<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\CodeGen\Schema\FieldDefinition;
use Dat0r\CodeGen\Schema\FieldDefinitionList;
use DOMXPath;
use DOMElement;

class FieldDefinitionXpathParser extends BaseXpathParser
{
    protected $short_names = array();

    public function parseXpath(DOMXPath $xpath, array $options = array())
    {
        $field_set = FieldDefinitionList::create();

        foreach ($xpath->query('./field', $options['context']) as $element) {
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
