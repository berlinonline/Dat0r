<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema;

class AggregateDefinitionXpathParser extends ModuleDefinitionXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array())
    {
        $aggregate_set = new Schema\ModuleDefinitionSet();

        $aggregate_node_list = $xpath->query(
            '//aggregate_definition',
            $options['context']
        );

        foreach ($aggregate_node_list as $aggregate_element)
        {
            $aggregate_set->add(
                $this->parseModuleDefinition($xpath, $aggregate_element)
            );
        }

        return $aggregate_set;
    }
}
