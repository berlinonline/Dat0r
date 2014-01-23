<?php

namespace Dat0r\CodeGen\Parser\ModuleSchema;

use Dat0r\CodeGen\Schema\ModuleDefinitionList;
use Dat0r\CodeGen\Schema\AggregateDefinition;
use DOMXPath;
use DOMElement;

class AggregateDefinitionXpathParser extends ModuleDefinitionXpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $aggregate_definitions_list = ModuleDefinitionList::create();
        $node_list = $xpath->query('//aggregate_definition', $context);
        foreach ($node_list as $element) {
            $aggregate_definitions_list->addItem(
                AggregateDefinition::create(
                    $this->parseModuleDefinition($xpath, $element)
                )
            );
        }

        return $aggregate_definitions_list;
    }
}
