<?php

namespace Dat0r\CodeGen\Parser;

interface IXpathParser
{
    public function parseXpath(\DOMXPath $xpath, array $options = array());
}
