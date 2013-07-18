<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\CodeGen\Schema;

class OptionDefinitionXpathParser extends BaseXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array())
    {
        $options_list = new Schema\OptionDefinitionList();

        foreach($xpath->query('./option', $options['context']) as $option_element)
        {
            $options_list->add(
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

        if ($element->hasAttribute('name'))
        {
            $name = $element->getAttribute('name');
        }

        $nested_options = $xpath->query('./option', $element);
        if ($nested_options->length > 0)
        {
            $value = new Schema\OptionDefinitionList();
            foreach ($nested_options as $option_element)
            {
                $value->add(
                    $this->parseOption($xpath, $option_element)
                );
            }
        }
        else
        {
            $value = trim($element->nodeValue);
        }

        return Schema\OptionDefinition::create(array(
            'name' => $name,
            'value' => $value,
            'default' => $default
        ));
    }
}
