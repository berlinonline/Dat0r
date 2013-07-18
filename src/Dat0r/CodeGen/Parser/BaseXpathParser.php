<?php

namespace Dat0r\CodeGen\Parser;

abstract class BaseXpathParser implements IXpathParser
{
    public static function create()
    {
        return new static();
    }

    protected function __construct() {}

    protected function parseDescription(\DOMXPath $xpath, \DOMElement $element)
    {
        return array_map(function($line)
        {
            return trim($line);
        }, preg_split ('/$\R?^/m', trim($element->nodeValue)));
    }

    protected function parseOptions(\DOMXPath $xpath, \DOMElement $element)
    {
        $parser = OptionDefinitionXpathParser::create();

        return $parser->parseXpath($xpath, array('context' => $element));
    }
}
