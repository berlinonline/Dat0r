<?php

namespace Dat0r\CodeGen\Parser;

use DOMXPath;

interface IXpathParser
{
    public function parseXpath(DOMXPath $xpath, array $options = array());
}
