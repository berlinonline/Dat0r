<?php

namespace Dat0r\CodeGen\Parser;

use Dat0r\Type\Object;

use DOMXPath;
use DOMElement;

abstract class BaseXpathParser extends Object implements IXpathParser
{
    protected function parseDescription(DOMXPath $xpath, DOMElement $element)
    {
        return array_map(
            function ($line) {
                return trim($line);
            },
            preg_split('/$\R?^/m', trim($element->nodeValue))
        );
    }

    protected function parseOptions(DOMXPath $xpath, DOMElement $element)
    {
        $parser = OptionDefinitionXpathParser::create();

        return $parser->parseXpath($xpath, array('context' => $element));
    }

    public static function literalize($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);
        if ($value == '') {
            return null;
        }

        $lc_value = strtolower($value);
        if ($lc_value === 'on' || $lc_value === 'yes' || $lc_value === 'true') {
            return true;
        } elseif ($lc_value === 'off' || $lc_value === 'no' || $lc_value === 'false') {
            return false;
        }

        if (preg_match('/^[0-9]+$/', $value)) {
            return (int)$value;
        }

        return $value;
    }
}
