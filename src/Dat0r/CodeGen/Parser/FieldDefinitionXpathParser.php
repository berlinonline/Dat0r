<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema;

class FieldDefinitionXpathParser implements IXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array())
    {
        $field_set = new Schema\FieldDefinitionSet();

        foreach($xpath->query('./field', $options['context']) as $field_element)
        {
            $field_set->add(
                $this->parseField($xpath, $field_element)
            );
        }

        return $field_set;
    }

    protected function parseField(\DOMXPath $xpath, \DOMElement $field_element)
    {
        $description = $this->parseDescription(
            $xpath,
            $xpath->query('./description', $field_element)->item(0)
        );

        return Schema\FieldDefinition::create(array(
            'name' => $field_element->getAttribute('name'),
            'type' => $field_element->getAttribute('type'),
            'description' => $description,
            'options' => $this->parseOptions($xpath, $field_element)
        ));
    }

    protected function parseDescription(\DOMXPath $xpath, \DOMElement $element)
    {
        return array_map(function($line)
        {
            return trim($line);
        }, preg_split ('/$\R?^/m', trim($element->nodeValue)));
    }

    protected function parseOptions(\DOMXPath $xpath, \DOMElement $element)
    {
        $parser = new OptionDefinitionXpathParser();

        return $parser->parseXpath(
            $xpath,
            array('context' => $element)
        );
    }
}
