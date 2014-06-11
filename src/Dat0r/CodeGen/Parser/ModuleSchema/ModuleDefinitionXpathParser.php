<?php

namespace Dat0r\CodeGen\Parser\ModuleSchema;

use Dat0r\Common\Error\RuntimeException;
use Dat0r\CodeGen\Schema\ModuleDefinition;
use DOMXPath;
use DOMElement;

class ModuleDefinitionXpathParser extends XpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $node_list = $xpath->query('./module_definition', $context);

        if ($node_list->length === 0) {
            throw new RuntimeException(
                "Missing module_definition node. Please check the given module_schema."
            );
        }

        return ModuleDefinition::create(
            $this->parseModuleDefinition($xpath, $node_list->item(0))
        );
    }

    protected function parseModuleDefinition(DOMXPath $xpath, DOMElement $element)
    {
        $implementor = null;
        $implementor_list = $xpath->query('./implementor', $element);
        if ($implementor_list->length > 0) {
            $implementor = $implementor_list->item(0)->nodeValue;
        }

        $document_implementor = null;
        $document_implementor_list = $xpath->query('./document_implementor', $element);
        if ($document_implementor_list->length > 0) {
            $document_implementor = $document_implementor_list->item(0)->nodeValue;
        }

        $description_node = $xpath->query('./description', $element)->item(0);
        if ($description_node) {
            $description = $this->parseDescription(
                $xpath,
                $xpath->query('./description', $element)->item(0)
            );
        } else {
            $description = '';
        }

        return array(
            'name' => $element->getAttribute('name'),
            'implementor' => $implementor,
            'document_implementor' => $document_implementor,
            'description' => $description,
            'options' => $this->parseOptions($xpath, $element),
            'attributes' => $this->parseAttributes($xpath, $element)
        );
    }

    protected function parseAttributes(DOMXPath $xpath, DOMElement $element)
    {
        $parser = AttributeDefinitionXpathParser::create();
        $attributes_element = $xpath->query('./attributes', $element)->item(0);

        return $parser->parseXpath($xpath, $attributes_element);
    }
}
