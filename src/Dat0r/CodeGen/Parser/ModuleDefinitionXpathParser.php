<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema;

class ModuleDefinitionXpathParser extends BaseXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array())
    {
        $node_list = $xpath->query('./module_definition', $options['context']);

        if ($node_list->length === 0) {
            throw new ParseException(
                "Missing module_definition node. Please check the given module_schema."
            );
        }

        return $this->parseModuleDefinition($xpath, $node_list->item(0));
    }

    protected function parseModuleDefinition(\DOMXPath $xpath, \DOMElement $element)
    {
        $implementor = null;
        $document_implementor = null;
        $description = $this->parseDescription(
            $xpath,
            $xpath->query('./description', $element)->item(0)
        );

        return Schema\ModuleDefinition::create(
            array(
                'name' => $element->getAttribute('name'),
                'implementor' => $implementor,
                'document_implementor' => $document_implementor,
                'description' => $description,
                'options' => $this->parseOptions($xpath, $element),
                'fields' => $this->parseFields($xpath, $element)
            )
        );
    }

    protected function parseFields(\DOMXPath $xpath, \DOMElement $element)
    {
        $parser = FieldDefinitionXpathParser::create();
        $fields_element = $xpath->query('./fields', $element)->item(0);

        return $parser->parseXpath($xpath, array('context' => $fields_element));
    }
}
