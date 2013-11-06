<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema\ModuleDefinitionList;
use Dat0r\CodeGen\Schema\AggregateDefinition;

class AggregateDefinitionXpathParser extends ModuleDefinitionXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array())
    {
        $aggregate_set = ModuleDefinitionList::create();

        $node_list = $xpath->query('//aggregate_definition', $options['context']);

        foreach ($node_list as $element) {
            $aggregate_set->addItem(
                AggregateDefinition::create(
                    $this->parseModuleDefinition($xpath, $element)
                )
            );
        }

        return $aggregate_set;
    }
}
