<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema;

class OptionDefinitionXpathParser extends BaseXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array())
    {
        $options_list = Schema\OptionDefinitionList::create();
        $options_nodelist = $xpath->query('./options', $options['context']);

        $option_nodes = null;
        if ($options_nodelist->length > 0) {
            $option_nodes = $xpath->query('./option', $options_nodelist->item(0));
        } else {
            $option_nodes = $xpath->query('./option', $options['context']);
        }

        foreach ($option_nodes as $option_element) {
            $options_list->addItem(
                $this->parseOption($xpath, $option_element)
            );
        }

        return $options_list;
    }

    protected function parseOption(\DOMXPath $xpath, \DOMElement $element)
    {
        $name = null;
        $value = null;
        $default = null;

        if ($element->hasAttribute('name')) {
            $name = $element->getAttribute('name');
        }

        $nested_options = $xpath->query('./option', $element);
        if ($nested_options->length > 0) {
            $value = Schema\OptionDefinitionList::create();
            foreach ($nested_options as $option_element) {
                $value->addItem(
                    $this->parseOption($xpath, $option_element)
                );
            }
        } else {
            $value = trim($element->nodeValue);
        }

        return Schema\OptionDefinition::create(
            array(
                'name' => $name,
                'value' => $this->literalize($value),
                'default' => $this->literalize($default)
            )
        );
    }
}
