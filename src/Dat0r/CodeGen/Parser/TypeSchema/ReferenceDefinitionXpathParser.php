<?php

namespace Dat0r\CodeGen\Parser\TypeSchema;

use Dat0r\CodeGen\Schema\TypeDefinitionList;
use Dat0r\CodeGen\Schema\ReferenceDefinition;
use DOMXPath;
use DOMElement;

class ReferenceDefinitionXpathParser extends TypeDefinitionXpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $reference_definitions_list = new TypeDefinitionList();
        $node_list = $xpath->query('//reference_definition', $context);

        foreach ($node_list as $element) {
            $reference_data = $this->parseTypeDefinition($xpath, $element);
            $reference_definition = new ReferenceDefinition($reference_data);
            $reference_definitions_list->addItem($reference_definition);
        }

        return $reference_definitions_list;
    }
}
