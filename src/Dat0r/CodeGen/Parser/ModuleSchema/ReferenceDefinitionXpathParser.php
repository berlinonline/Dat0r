<?php

namespace Dat0r\CodeGen\Parser\ModuleSchema;

use Dat0r\CodeGen\Schema\ModuleDefinitionList;
use Dat0r\CodeGen\Schema\ReferenceDefinition;
use DOMXPath;
use DOMElement;

class ReferenceDefinitionXpathParser extends ModuleDefinitionXpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $reference_definitions_list = ModuleDefinitionList::create();
        $node_list = $xpath->query('//reference_definition', $context);
        foreach ($node_list as $element) {
            $reference_definitions_list->addItem(
                ReferenceDefinition::create(
                    $this->parseModuleDefinition($xpath, $element)
                )
            );
        }

        return $reference_definitions_list;
    }
}
