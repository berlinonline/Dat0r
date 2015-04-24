<?php

namespace Dat0r\CodeGen\Parser\Schema;

use Dat0r\CodeGen\Schema\EntityTypeDefinitionList;
use Dat0r\CodeGen\Schema\EmbedDefinition;
use DOMXPath;
use DOMElement;

class EmbedDefinitionXpathParser extends EntityTypeDefinitionXpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $embed_definitions_list = new EntityTypeDefinitionList();
        $node_list = $xpath->query('//embed_definition', $context);

        foreach ($node_list as $element) {
            $embed_type_data = $this->parseEntityTypeDefinition($xpath, $element);
            $embed_definition = new EmbedDefinition($embed_type_data);
            $embed_definitions_list->addItem($embed_definition);
        }

        return $embed_definitions_list;
    }
}
