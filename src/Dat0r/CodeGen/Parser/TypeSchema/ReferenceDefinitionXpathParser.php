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
        $reference_definitions_list = TypeDefinitionList::create();
        $node_list = $xpath->query('//reference_definition', $context);
        foreach ($node_list as $element) {
            $reference_definitions_list->addItem(
                ReferenceDefinition::create(
                    $this->parseTypeDefinition($xpath, $element)
                )
            );
        }

        return $reference_definitions_list;
    }
}
