<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema;

class FieldDefinitionXpathParser extends BaseXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array())
    {
        $field_set = Schema\FieldDefinitionSet::create();

        foreach ($xpath->query('./field', $options['context']) as $element) {
            $field_set->add($this->parseField($xpath, $element));
        }

        return $field_set;
    }

    protected function parseField(\DOMXPath $xpath, \DOMElement $element)
    {
        $description = '';

        if (($description_element = $xpath->query('./description', $element)->item(0))) {
            $description = $this->parseDescription(
                $xpath,
                $xpath->query('./description', $element)->item(0)
            );
        }

        return Schema\FieldDefinition::create(
            array(
                'name' => $element->getAttribute('name'),
                'type' => $element->getAttribute('type'),
                'description' => $description,
                'options' => $this->parseOptions($xpath, $element)
            )
        );
    }
}
