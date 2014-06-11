<?php

namespace Dat0r\CodeGen\Parser\TypeSchema;

use Dat0r\CodeGen\Schema\TypeDefinitionList;
use Dat0r\CodeGen\Schema\AggregateDefinition;
use DOMXPath;
use DOMElement;

class AggregateDefinitionXpathParser extends TypeDefinitionXpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $aggregate_definitions_list = TypeDefinitionList::create();
        $node_list = $xpath->query('//aggregate_definition', $context);
        foreach ($node_list as $element) {
            $aggregate_definitions_list->addItem(
                AggregateDefinition::create(
                    $this->parseTypeDefinition($xpath, $element)
                )
            );
        }

        return $aggregate_definitions_list;
    }
}
